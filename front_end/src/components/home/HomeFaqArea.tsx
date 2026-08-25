import OnlineDocumentationSvg from '@/svg/OnlineDocumentationSvg';
import SupportSvg from '@/svg/SupportSvg';
import Link from 'next/link';
import React from 'react';

interface FaqItemProps {
    id: string;
    question: string;
    answer: string;
}

const faqData: FaqItemProps[] = [
    { id: "Nine", question: "What is iStudy? How does it work?", answer: "iStudy is a feature-rich HTML5 template designed specifically for educational platforms. It provides a variety of pre-designed layouts, including those for universities, online courses, training centers, and more. The template allows for easy customization and offers a well-documented, clean code base to create powerful, professional educational websites." },
    { id: "Ten", question: "Can I get free support?", answer: "Yes, we provide free support to iStudy users. Our dedicated support team is ready to assist with any technical issues or questions." },
    { id: "Eleven", question: "Can I customize elements as I like?", answer: "Absolutely! iStudy is designed with flexibility in mind. You can easily customize various elements to match your specific needs." },
    { id: "Twelve", question: "Do you have online documentation?", answer: "Yes, iStudy comes with comprehensive online documentation. This guides you through template installation, customization, and troubleshooting." },
    { id: "Thirteen", question: "Can I build a complete website with this template?", answer: "Yes, iStudy is a fully functional HTML5 template that allows you to build a complete educational website with pre-built pages and customizability." },
];
const FaqItem: React.FC<FaqItemProps> = ({ id, question, answer }) => {
    const isFirst = id === faqData[0].id;

    return (
        <div className="accordion-item">
            <h6 className="accordion-header" id={`heading${id}`}>
                <button 
                    className={`accordion-button ${isFirst ? '' : 'collapsed'}`} 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target={`#collapse${id}`} 
                    aria-expanded={isFirst ? "true" : "false"} 
                    aria-controls={`collapse${id}`}
                >
                    <span>{`Q${id.replace(/\D/g, '')}:`}</span> {question}
                </button>
            </h6>
            <div 
                id={`collapse${id}`} 
                className={`accordion-collapse collapse ${isFirst ? 'show' : ''}`} 
                aria-labelledby={`heading${id}`} 
                data-bs-parent="#accordionExampleThree"
            >
                <div className="accordion-body">{answer}</div>
            </div>
        </div>
    );
};

interface ServiceBoxProps {
    icon: React.ElementType;
    title: string;
    link: string;
    btnText: string;
    btnClass: string;
}

const ServiceBox: React.FC<ServiceBoxProps> = ({ icon: Icon, title, link, btnText, btnClass }) => (
    <div className="col-lg-12 col-md-6">
        <div className="bd-landing-service">
            <div className="inner">
                <span className="inner-icon">
                    <Icon />
                </span>
                <div className="content">
                    <h4 className="title mb-15">{title}</h4>
                    <Link href={link} target="_blank" className={`bd-btn ${btnClass}`}>
                        {btnText}
                    </Link>
                </div>
            </div>
        </div>
    </div>
);

const HomeFaqArea: React.FC = () => {
    return (
        <div className="faq-area section-space">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-lg-12">
                        <div className="section-title-wrapper text-center section-title-space">
                            <span className="bd-section-subtitle">ANY QUESTION</span>
                            <h2 className="bd-section-title mb-15">Do you have any Question?</h2>
                            <p className="description">Check out our FAQ section to see if we can help</p>
                        </div>
                    </div>
                </div>

                <div className="row gy-30 align-items-center justify-content-between">
                    <div className="col-lg-7 col-sm-12 col-12">
                        <div className="demo-faq">
                            <div className="accordion-common-style accordion-transparent accordion-style-one">
                                <div className="accordion" id="accordionExampleThree">
                                    {faqData.map((faq) => (
                                        <FaqItem key={faq.id} {...faq} />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-lg-5 col-sm-12 col-12">
                        <div className="row gy-30">
                            <ServiceBox 
                                icon={OnlineDocumentationSvg} 
                                title="Online Documentation" 
                                link="#" 
                                btnText="View Documentation" 
                                btnClass="btn-primary" 
                            />
                            <ServiceBox 
                                icon={SupportSvg} 
                                title="Dedicated Support" 
                                link="https://support.topylo.com" 
                                btnText="Get Support" 
                                btnClass="btn-secondary" 
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default HomeFaqArea;