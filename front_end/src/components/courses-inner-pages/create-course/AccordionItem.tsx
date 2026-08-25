import React from "react";

interface AccordionItemProps {
  id: string | number;
  title: string;
  component: React.FC;
}

const AccordionItem: React.FC<AccordionItemProps> = ({ id, title, component: Component }) => (
  <div className="accordion-item">
    <h2 className="accordion-header" id={`heading${id}`}>
      <button
        className="accordion-button collapsed"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target={`#collapse${id}`}
        aria-expanded="false"
        aria-controls={`collapse${id}`}
      >
        {title}
      </button>
    </h2>
    <div id={`collapse${id}`} className="accordion-collapse collapse" aria-labelledby={`heading${id}`} data-bs-parent="#accordionExample">
      <div className="accordion-body">
        <Component />
      </div>
    </div>
  </div>
);

export default AccordionItem;
