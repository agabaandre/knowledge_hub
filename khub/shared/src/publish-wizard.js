/**
 * The publish-a-resource wizard, shared by backend and frontend.
 *
 * The step/field definition below drives the React wizard (rendering, step
 * validation) AND the API validation, so the two can never drift. It mirrors
 * the Laravel two-step SmartWizard (account/wizard.blade.php) and its
 * PublicationSubmissionValidation rules, including the admin-configurable
 * required fields (`setting.publication_required_fields`) and minimum
 * description length (`setting.publication_min_words`).
 */

/** Laravel's defaults when publication_required_fields is not configured. */
export const DEFAULT_REQUIRED_FIELDS = Object.freeze({
  title: true,
  description: true,
  associated_authors: true,
  tags: true,
  theme: true,
  sub_theme: true,
  data_category_id: true,
  year_published: false,
  doi: false,
  issn: false,
  isbn: false,
  license_id: false,
  copyright_info: false,
});

export const UPLOAD_TYPES = Object.freeze(['attachment', 'link']);

export const PUBLISH_WIZARD = Object.freeze({
  steps: [
    {
      id: 'start',
      label: 'Step 1',
      title: 'Start Here',
      fields: [
        { name: 'upload_type', type: 'upload-type', label: 'Attachment or external link' },
        { name: 'is_embedded', type: 'toggle', label: 'Embedded On Page' },
        { name: 'is_default', type: 'toggle', label: 'Default in Category', adminOnly: true },
        { name: 'admin_only', type: 'toggle', label: 'Admin Only Access', adminOnly: true },
        { name: 'show_disclaimer', type: 'toggle', label: 'Shows Disclaimer', default: true },
        { name: 'title', type: 'text', label: 'Resource Title', requiredKey: 'title', max: 500, hint: 'A clear title makes the resource easy to find and understand.' },
        { name: 'link', type: 'url', label: 'Publication URL Link', max: 500, half: true, hint: 'Required when you are not uploading a file.' },
        { name: 'year_published', type: 'year', label: 'Year of Publication', requiredKey: 'year_published', half: true },
        { name: 'file', type: 'file', label: 'Resource file', accept: '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.csv,.png,.jpg,.jpeg', hint: 'PDF is the recommended format · max 25 MB' },
        { name: 'data_category_id', type: 'select', label: 'Category', requiredKey: 'data_category_id', options: 'dataCategories', half: true },
        { name: 'category_id', type: 'select', label: 'Sub Category', options: 'fileCategories', half: true },
        { name: 'theme', type: 'select', label: 'Thematic Area', requiredKey: 'theme', options: 'themes', half: true },
        { name: 'sub_theme', type: 'select', label: 'Sub Theme', requiredKey: 'sub_theme', options: 'subThemes', dependsOn: 'theme', half: true },
        { name: 'rccs', type: 'multiselect', label: 'Region', options: 'regions', half: true, hint: 'Tip: if this resource applies to every member state, choose All.' },
        { name: 'countries', type: 'multiselect', label: 'Member States', options: 'countries', dependsOn: 'rccs', half: true },
      ],
    },
    {
      id: 'details',
      label: 'Step 2',
      title: 'Details & Attachments',
      fields: [
        { name: 'description', type: 'richtext', label: 'Description / Abstract', requiredKey: 'description', hint: 'Describe the resource; the minimum length is set by the hub administrator.' },
        { name: 'tags', type: 'tags', label: 'Tags / Health Topics', requiredKey: 'tags', options: 'tags' },
        { name: 'associated_authors', type: 'text', label: 'Associated Authors', requiredKey: 'associated_authors', max: 1000, half: true },
        { name: 'author_affiliation', type: 'text', label: 'Author Affiliation', required: 'always', max: 1000, half: true },
        { name: 'cover', type: 'file', label: 'Cover image (optional)', accept: '.png,.jpg,.jpeg,.webp', hint: 'Shown on cards and search results.' },
        { name: 'publisher', type: 'text', label: 'Publisher', max: 500, half: true },
        { name: 'funder', type: 'text', label: 'Funder', max: 500, half: true },
        { name: 'doi', type: 'text', label: 'DOI', requiredKey: 'doi', max: 255, half: true },
        { name: 'issn', type: 'text', label: 'ISSN', requiredKey: 'issn', max: 50, half: true },
        { name: 'isbn', type: 'text', label: 'ISBN', requiredKey: 'isbn', max: 50, half: true },
        { name: 'license_id', type: 'select', label: 'License', requiredKey: 'license_id', options: 'licenses', half: true },
        { name: 'is_journal_article', type: 'toggle', label: 'This is a journal article' },
        { name: 'journal_name', type: 'text', label: 'Journal Name', max: 500, showIf: 'is_journal_article', half: true },
        { name: 'journal_volume', type: 'text', label: 'Volume', max: 50, showIf: 'is_journal_article', half: true },
        { name: 'journal_issue', type: 'text', label: 'Issue', max: 50, showIf: 'is_journal_article', half: true },
        { name: 'journal_pages', type: 'text', label: 'Pages', max: 50, showIf: 'is_journal_article', half: true },
        { name: 'copyright_info', type: 'richtext', label: 'Copyright information', requiredKey: 'copyright_info' },
      ],
    },
  ],
});

const stripTags = (html) => String(html ?? '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();

const isRequired = (field, requiredFields) =>
  field.required === 'always' || (field.requiredKey ? (requiredFields[field.requiredKey] ?? DEFAULT_REQUIRED_FIELDS[field.requiredKey] ?? false) : false);

const empty = (value) => value === undefined || value === null || value === '' || (Array.isArray(value) && value.length === 0);

/**
 * Validate a submission. Pure and environment-free, so the API and the wizard
 * run the identical rules.
 *
 * @param {Record<string, unknown>} data normalized values (arrays for tags/countries/rccs, booleans for toggles)
 * @param {{requiredFields?: Record<string, boolean>, minWords?: number, isAdmin?: boolean, hasFile?: boolean}} context
 * @returns {{ valid: boolean, errors: Record<string, string>, steps: Record<string, string[]> }}
 */
export const validatePublishSubmission = (data, context = {}) => {
  const requiredFields = { ...DEFAULT_REQUIRED_FIELDS, ...(context.requiredFields ?? {}) };
  const minWords = Number(context.minWords ?? 150);
  const minChars = minWords * 5;
  const errors = {};

  for (const step of PUBLISH_WIZARD.steps) {
    for (const field of step.fields) {
      if (field.adminOnly && !context.isAdmin) continue;
      if (field.showIf && !data[field.showIf]) continue;
      const value = data[field.name];
      if (isRequired(field, requiredFields) && empty(value) && !['file', 'upload-type'].includes(field.type)) {
        errors[field.name] = `${field.label} is required.`;
        continue;
      }
      if (!empty(value) && field.max && String(value).length > field.max) {
        errors[field.name] = `${field.label} must be at most ${field.max} characters.`;
      }
    }
  }

  // Cross-field rules, matching the Laravel validator.
  const uploadType = data.upload_type ?? 'attachment';
  if (!UPLOAD_TYPES.includes(uploadType)) errors.upload_type = 'Choose Attachment or External Link.';
  if (uploadType === 'link' && empty(data.link)) errors.link = 'Paste the URL of the externally hosted resource.';
  if (uploadType === 'attachment' && !context.hasFile && empty(data.link)) {
    errors.file = 'Upload a file, or switch to External Link and paste a URL.';
  }
  if (!empty(data.link) && !/^https?:\/\/\S+$/.test(String(data.link))) errors.link = 'The link must be a valid http(s) URL.';

  const description = stripTags(data.description);
  if (!errors.description && (requiredFields.description || !empty(data.description)) && description.length < minChars) {
    errors.description = `The description must be at least ${minWords} words (about ${minChars} characters); currently ${description.length}.`;
  }

  if (!empty(data.year_published)) {
    const year = Number(data.year_published);
    if (!Number.isInteger(year) || year < 1900 || year > new Date().getFullYear()) {
      errors.year_published = `Year must be between 1900 and ${new Date().getFullYear()}.`;
    }
  }

  const regionsAll = (Array.isArray(data.rccs) ? data.rccs : []).some((value) => String(value).toLowerCase() === 'all');
  if (!regionsAll && empty(data.countries)) {
    errors.countries = 'Please select at least one member state (or choose the All region).';
  }

  // Per-step error buckets for the wizard UI.
  const steps = {};
  for (const step of PUBLISH_WIZARD.steps) {
    const names = new Set(step.fields.map((field) => field.name));
    names.add('file');
    steps[step.id] = Object.keys(errors).filter((name) => names.has(name)).map((name) => errors[name]);
  }

  return { valid: Object.keys(errors).length === 0, errors, steps };
};
