import Image from 'next/image';
import React from 'react';
import faqThumb from '../../../public/assets/images/faq/faq-thumb-02.webp';

// FAQ Item Type
interface FaqItemProps {
    id: string;
    question: string;
    answer: string;
    defaultExpanded?: boolean;
}

// Reusable FaqItem Component
const FaqItem: React.FC<FaqItemProps> = ({ id, question, answer, defaultExpanded = false }) => {
    return (
        <div className="accordion-item">
            <h2 className="accordion-header" id={`heading${id}`}>
                <button
                    className={`accordion-button ${defaultExpanded ? '' : 'collapsed'}`}
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target={`#collapse${id}`}
                    aria-expanded={defaultExpanded}
                    aria-controls={`collapse${id}`}
                >
                    {question}
                </button>
            </h2>
            <div
                id={`collapse${id}`}
                className={`accordion-collapse collapse ${defaultExpanded ? 'show' : ''}`}
                aria-labelledby={`heading${id}`}
                data-bs-parent="#accordionExampleTen"
            >
                <div className="accordion-body">{answer}</div>
            </div>
        </div>
    );
};

// FAQ Data
const faqData: FaqItemProps[] = [
    {
        id: 'TwentyEight',
        question: 'How does the school ensure personalized learning?',
        answer: 'We keep class sizes small to ensure individual attention and use differentiated instruction to cater to different learning styles. Teachers work closely with students to identify their strengths and areas for improvement, providing tailored support to help them succeed.',
        defaultExpanded: true, // First item should be expanded by default
    },
    {
        id: 'TwentyNine',
        question: 'How are parents involved in the school community?',
        answer: 'Parents are encouraged to engage with the school through regular communication, parent-teacher conferences, and volunteer opportunities. We also have a parent advisory board that collaborates with school leadership on key initiatives.',
    },
    {
        id: 'Thirty',
        question: 'What extracurricular activities are offered?',
        answer: 'We offer a wide range of extracurricular activities, including sports, arts, robotics, coding clubs, debate teams, and community service opportunities. These activities are designed to help students explore their interests and develop new skills outside the classroom.',
    },
];

// Main FAQ Component
const ModernSchoolingFaqArea: React.FC = () => {
    return (
        <section className="bd-faq-area section-space fix">
            <div className="container">
                <div className="row g-30 align-items-center justify-content-between justify-content-md-center">
                    {/* FAQ Image */}
                    <div className="col-xl-6 col-lg-6 col-md-10">
                        <div className="bd-faq-thumb">
                            <Image src={faqThumb} style={{ width: "auto", height: "auto" }} priority alt="FAQ Image" />
                        </div>
                    </div>

                    {/* FAQ Content */}
                    <div className="col-xl-6 col-lg-6">
                        <div className="bd-section-title-wrapper section-title-space">
                            <span className="bd-section-subtitle">{`FAQ's`}</span>
                            <h2 className="bd-section-title">Frequently Asked Questions</h2>
                        </div>
                        <div className="bd-faq-content modern-schooling">
                            <div className="accordion-common-style accordion-bg accordion-theme-bg accordion-item-margin">
                                <div className="accordion" id="accordionExampleTen">
                                    {faqData.map((faq) => (
                                        <FaqItem key={faq.id} {...faq} />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default ModernSchoolingFaqArea;
