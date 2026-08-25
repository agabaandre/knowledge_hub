import Image from 'next/image';
import React from 'react';
import aboutImage from '../../../public/assets/images/about/about-language.webp';
import Link from 'next/link';

// Type for FeatureItem props
interface FeatureItemProps {
    icon: string;
    title: string;
    description: string;
}

const FeatureItem: React.FC<FeatureItemProps> = ({ icon, title, description }) => (
    <div className="bd-feature-list">
        <div className="icon">
            <i className={icon}></i>
        </div>
        <div className="bd-feature-list-content">
            <h6 className="title">{title}</h6>
            <p className="description">{description}</p>
        </div>
    </div>
);

const LanguageAcademyAbout: React.FC = () => {
    return (
        <>
            {/* -- about area start -- */}
            <section className="bd-about-area section-space">
                <div className="container">
                    <div className="row gy-30 align-items-center align-items-lg-start justify-content-between">
                        <div className="col-12 col-xl-6 col-lg-6">
                            <div className="bd-about-thumbnail">
                                <Image className="w-100" style={{width:"100%", height:"auto"}} src={aboutImage} alt="about-language" />
                            </div>
                        </div>
                        <div className="col-12 col-xl-6 col-lg-6">
                            <div className="bd-about-wrapper style-three">
                                <div className="bd-about-content-wrapper">
                                    <div className="bd-section-title-wrapper">
                                        <span className="bd-section-subtitle">About Us</span>
                                        <h2 className="bd-section-title mb-20">Join the Language Learning Revolution</h2>
                                        <p className="bd-section-paragraph">
                                            At our Language Academy, we believe that learning a new language opens doors to new cultures, opportunities, and connections.
                                            Our mission is to empower you to communicate confidently and fluently in the language of your choice.
                                        </p>
                                    </div>

                                    {/* Feature List */}
                                    <div className="bd-feature-list-wrapper">
                                        <FeatureItem
                                            icon="icon-online-class"
                                            title="Certified Language Experts"
                                            description="Our instructors are native speakers and certified language experts with years of teaching experience, ensuring you learn from the best."
                                        />
                                        <FeatureItem
                                            icon="icon-online-learning"
                                            title="Structured & Immersive Curriculum"
                                            description="Our courses are designed to immerse you in the language, covering grammar, vocabulary, speaking, listening, and cultural nuances."
                                        />
                                        <FeatureItem
                                            icon="icon-career-growth"
                                            title="Tailored Learning Paths"
                                            description="We create personalized learning plans to match your goals, pace, and learning style, ensuring you achieve fluency faster."
                                        />
                                    </div>

                                    {/* Call to Action */}
                                    <div className="bd-about-btn d-flex align-items-center gap-30 flex-wrap">
                                        <Link className="bd-btn btn-outline-primary" href="/about-online-course">
                                            Discover More
                                        </Link>
                                        <div className="bd-about-contact">
                                            <Link className="bd-btn-cta" href="tel:9670192425990">
                                                <span><i className="fa-regular fa-phone-volume"></i></span> +967 019 2425 990
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- about area end -- */}
        </>
    );
};

export default LanguageAcademyAbout;
