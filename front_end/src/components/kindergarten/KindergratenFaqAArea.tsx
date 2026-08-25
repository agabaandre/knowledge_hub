import Image from 'next/image';
import React from 'react';
import faqThumb from '../../../public/assets/images/faq/faq-thumb-03.webp';
import faqGroupStartImg from '../../../public/assets/images/shape/faq-star-group.webp';

// Define the type for FAQ items
interface FAQItemProps {
    question: string;
    answer: string;
    id: string;
}

// FAQ data array with proper types
const faqData: FAQItemProps[] = [
    {
        id: 'TwentyEight',
        question: 'How does the school ensure personalized learning?',
        answer: 'We keep class sizes small to ensure individual attention and use differentiated instruction to cater to different learning styles. Teachers work closely with students to identify their strengths and areas for improvement, providing tailored support to help them succeed.',
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
    }
];

const FAQItem: React.FC<FAQItemProps & { isActive: boolean }> = ({ id, question, answer, isActive }) => (
    <div className="accordion-item">
        <h2 className="accordion-header" id={`heading${id}`}>
            <button
                className={`accordion-button ${isActive ? '' : 'collapsed'}`}
                type="button"
                data-bs-toggle="collapse"
                data-bs-target={`#collapse${id}`}
                aria-expanded={isActive ? "true" : "false"}
                aria-controls={`collapse${id}`}
            >
                {question}
            </button>
        </h2>
        <div 
            id={`collapse${id}`} 
            className={`accordion-collapse collapse ${isActive ? 'show' : ''}`} 
            aria-labelledby={`heading${id}`} 
            data-bs-parent="#accordionExampleTen"
        >
            <div className="accordion-body">{answer}</div>
        </div>
    </div>
);

const KindergartenFaqArea = () => {
    return (
        <section className="bd-faq-area p-relative secondary-bg-05 section-space">
            <div className="container">
                <div className="row gy-30 align-items-center justify-content-between">
                    {/* FAQ Content Section */}
                    <div className="col-xl-6 col-lg-6">
                        <div className="bd-section-title-wrapper section-title-space">
                            <span className="bd-section-subtitle">FAQ&apos;s</span>
                            <h2 className="bd-section-title">Know More About iStudy</h2>
                        </div>
                        <div className="bd-faq-content">
                            <div className="accordion-common-style accordion-bg accordion-theme-bg accordion-item-margin">
                                <div className="accordion" id="accordionExampleTen">
                                    {faqData.map((item, index) => (
                                        <FAQItem key={item.id} {...item} isActive={index === 0} />
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* FAQ Image Section */}
                    <div className="col-xl-6 col-lg-6">
                        <div className="bd-faq-thumb-wrapper">
                            <div className="bd-faq-thumb-two">
                                <Image style={{width:"auto", height:"auto"}} src={faqThumb} alt="FAQ" />
                            </div>
                            <div className="bd-faq-shape d-none d-lg-block">
                                {[1, 2].map((index) => (
                                    <div key={index} className={`shape-${index}`}>
                                        <Image src={faqGroupStartImg} alt="Shape" />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default KindergartenFaqArea;
