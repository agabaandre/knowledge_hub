import { useState } from 'react';
import { Seo } from '../../core/Seo.jsx';
import { useApi } from '../../core/hooks.js';
import { Page, Loading } from '../../core/Page.jsx';

/** FAQs with FAQPage structured data; the accordion is React-controlled. */
export const FaqsPage = () => {
  const { data, error, loading } = useApi('/api/faqs');
  const [open, setOpen] = useState(0);
  const faqs = data?.data ?? [];
  const jsonLd = faqs.length
    ? { '@context': 'https://schema.org', '@type': 'FAQPage', mainEntity: faqs.map((faq) => ({ '@type': 'Question', name: faq.question, acceptedAnswer: { '@type': 'Answer', text: faq.answer } })) }
    : null;

  return (
    <Page title="Frequently Asked Questions" crumb="FAQs">
      <Seo title="Frequently asked questions" path="/faqs" description="Frequently asked questions about using the Knowledge Hub, accounts, submissions, and support." jsonLd={jsonLd} />
      {error && <div className="alert alert-danger" role="alert">{error.message}</div>}
      {loading && <Loading />}
      <div className="row justify-content-center">
        <div className="col-lg-9">
          {faqs.map((faq, index) => (
            <div className="card mb-2" key={faq.id} style={{ border: '1px solid #e2e8f0', borderRadius: 4 }}>
              <button
                type="button"
                className="card-header bg-white d-flex align-items-center justify-content-between w-100 text-left"
                style={{ border: 0, cursor: 'pointer', fontWeight: 600, color: '#0f172a' }}
                aria-expanded={open === index}
                onClick={() => setOpen(open === index ? -1 : index)}
              >
                <span>{faq.question}</span>
                <i className={`fa fa-angle-${open === index ? 'up' : 'down'}`} aria-hidden="true" />
              </button>
              {open === index && (
                <div className="card-body" dangerouslySetInnerHTML={{ __html: faq.answer }} />
              )}
            </div>
          ))}
        </div>
      </div>
    </Page>
  );
};
