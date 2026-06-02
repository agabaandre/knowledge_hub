<script>
(function (global) {
    var MINOR_WORDS = {
        a: 1, an: 1, and: 1, as: 1, at: 1, but: 1, by: 1, for: 1, from: 1, in: 1, into: 1,
        nor: 1, of: 1, on: 1, onto: 1, or: 1, per: 1, so: 1, the: 1, to: 1, up: 1, via: 1,
        with: 1, yet: 1, vs: 1, v: 1
    };

    function cleanTitleInput(title) {
        if (title == null) {
            return '';
        }
        return String(title)
            .replace(/<[^>]*>/g, '')
            .replace(/[\u200B-\u200D\uFEFF\u200E\u200F\u2060-\u206F\u202A-\u202E\u2066-\u2069\u00AD]/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function normalizeTitleCaseWord(word, isEdgeWord) {
        if (!word) {
            return word;
        }

        var match = word.match(/^([("“'\[]*)(.*?)([)\]"”',.!?:;]*)$/);
        if (!match) {
            return word;
        }

        var prefix = match[1] || '';
        var core = match[2] || '';
        var suffix = match[3] || '';

        if (!core) {
            return word;
        }

        var parts = core.split(/([\-\/])/);
        for (var i = 0; i < parts.length; i++) {
            var part = parts[i];
            if (part === '-' || part === '/') {
                continue;
            }

            var trimmed = part.trim();
            if (!trimmed) {
                continue;
            }

            var lower = trimmed.toLocaleLowerCase('en');
            if (/^[A-Z0-9][A-Z0-9\-&]{1,}$/.test(trimmed)
                && trimmed.length <= 12
                && !MINOR_WORDS[lower]
                && (/[0-9&]/.test(trimmed) || trimmed.length === 3)) {
                parts[i] = trimmed;
                continue;
            }

            if (!isEdgeWord && MINOR_WORDS[lower]) {
                parts[i] = lower;
                continue;
            }

            parts[i] = lower.charAt(0).toLocaleUpperCase('en') + lower.slice(1);
        }

        return prefix + parts.join('') + suffix;
    }

    function normalizeTitleCaseLocal(title) {
        title = cleanTitleInput(title);
        if (!title) {
            return title;
        }

        var tokens = title.split(/(\s+)/);
        var wordIndexes = [];
        for (var i = 0; i < tokens.length; i++) {
            if (!/^\s+$/.test(tokens[i])) {
                wordIndexes.push(i);
            }
        }
        if (!wordIndexes.length) {
            return title;
        }

        var first = wordIndexes[0];
        var last = wordIndexes[wordIndexes.length - 1];
        for (var w = 0; w < wordIndexes.length; w++) {
            var idx = wordIndexes[w];
            var isEdge = (idx === first || idx === last);
            tokens[idx] = normalizeTitleCaseWord(tokens[idx], isEdge);
        }

        return tokens.join('');
    }

    function formatTitleCaseInput(el) {
        if (!el || typeof el.value !== 'string') {
            return;
        }
        var formatted = normalizeTitleCaseLocal(el.value);
        if (formatted !== el.value) {
            el.value = formatted;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function bindTitleCaseInputs(root) {
        root = root || document;
        root.querySelectorAll('input.js-format-title-case, textarea.js-format-title-case').forEach(function (el) {
            if (el.dataset.titleCaseBound === '1') {
                return;
            }
            el.dataset.titleCaseBound = '1';
            el.addEventListener('blur', function () {
                formatTitleCaseInput(el);
            });
        });
    }

    function applyTitleCaseToForm(formSelector) {
        var form = typeof formSelector === 'string'
            ? document.querySelector(formSelector)
            : formSelector;
        if (!form) {
            return;
        }
        form.querySelectorAll('input.js-format-title-case, textarea.js-format-title-case').forEach(formatTitleCaseInput);
    }

    global.normalizeTitleCaseLocal = normalizeTitleCaseLocal;
    global.formatTitleCaseInput = formatTitleCaseInput;
    global.bindTitleCaseInputs = bindTitleCaseInputs;
    global.applyTitleCaseToForm = applyTitleCaseToForm;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            bindTitleCaseInputs();
        });
    } else {
        bindTitleCaseInputs();
    }
})(window);
</script>
