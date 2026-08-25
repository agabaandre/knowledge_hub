import Image from 'next/image';
import React from 'react';
import FaqThumb1 from '../../../public/assets/images/faq/faq-thumb-04.webp';
import FaqThumb2 from '../../../public/assets/images/faq/faq-thumb-05.webp';
import FaqThumb3 from '../../../public/assets/images/faq/faq-thumb-06.webp';

interface FaqItem {
    id: string;
    question: string;
    answer: string;
}

const faqData: FaqItem[] = [
    {
        id: "TwentyEight",
        question: "Who can enroll in the Quran learning courses?",
        answer:
            "Anyone, regardless of their age or experience, can enroll in our Quran learning courses. We offer courses for beginners, intermediate, and advanced learners, so everyone can find a suitable level of study.",
    },
    {
        id: "TwentyNine",
        question: "Do I need prior knowledge of Arabic to take the courses?",
        answer:
            "No prior knowledge of Arabic is required to join our courses. Our courses are structured to start from the basics, including learning the Arabic alphabet, pronunciation, and Quranic vocabulary, making them accessible for beginners.",
    },
    {
        id: "Thirty",
        question: "What is Tajweed, and why is it important?",
        answer:
            "Tajweed refers to the set of rules that govern the correct pronunciation of Arabic letters and the proper rhythm in reciting the Quran. It is essential to learn Tajweed to ensure that the Quran is recited as intended, preserving the meaning and integrity of the words.",
    },
    {
        id: "Forty",
        question: "How can I improve my Quran recitation?",
        answer:
            "To improve your Quran recitation, it is important to practice regularly, listen to skilled reciters, and focus on learning Tajweed rules. Our courses offer personalized guidance, which will help you progress step by step in mastering proper recitation techniques.",
    },
];

const faqImages = [FaqThumb1, FaqThumb2, FaqThumb3];

const QuranLearningFaq: React.FC = () => {
    return (
        <section className="bd-faq-area section-space primary-bg">
            <div className="container">
                <div className="row gy-30 align-items-center">
                    {/* FAQ Images */}
                    <div className="col-xl-6 col-lg-6">
                        <div className="d-flex gap-20 flex-wrap">
                            <div className="bd-faq-thumb style-three">
                                <Image src={faqImages[0]} alt="FAQ Image 1" />
                            </div>
                            <div className="bd-faq-small-thumb">
                                {faqImages.slice(1).map((image, index) => (
                                    <div key={index} className="bd-faq-thumb style-three has-small">
                                        <Image src={image} alt={`FAQ Image ${index + 2}`} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* FAQ Content */}
                    <div className="col-xl-6 col-lg-6">
                        <div className="bd-section-title-wrapper section-title-space">
                            <span className="bd-section-subtitle">Quran Learning {`FAQ's`}</span>
                            <h2 className="bd-section-title">Frequently Asked Questions</h2>
                        </div>
                        <div className="bd-faq-content has-white-bg">
                            <div className="accordion-common-style accordion-bg accordion-theme-bg accordion-item-margin">
                                <div className="accordion" id="accordionExampleTen">
                                    {faqData.map(({ id, question, answer }, index) => (
                                        <div key={id} className="accordion-item">
                                            <h2 className="accordion-header" id={`heading${id}`}>
                                                <button
                                                    className={`accordion-button ${index === 0 ? '' : 'collapsed'}`}
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target={`#collapse${id}`}
                                                    aria-expanded={index === 0 ? "true" : "false"}
                                                    aria-controls={`collapse${id}`}
                                                >
                                                    {question}
                                                </button>
                                            </h2>
                                            <div
                                                id={`collapse${id}`}
                                                className={`accordion-collapse collapse ${index === 0 ? 'show' : ''}`}
                                                aria-labelledby={`heading${id}`}
                                                data-bs-parent="#accordionExampleTen"
                                            >
                                                <div className="accordion-body">{answer}</div>
                                            </div>
                                        </div>
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

export default QuranLearningFaq;
