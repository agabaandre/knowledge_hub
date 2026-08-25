import Image, { StaticImageData } from 'next/image';
import Link from 'next/link';
import React from 'react';
import aboutThumb from '../../../public/assets/images/about/about-thumb-07.webp';
import { featuresData } from '@/data/feature-data';

// Define types for FeatureItem props
interface FeatureItemProps {
    icon: string;
    title: string;
    description: string;
}

// Reusable FeatureItem Component
const FeatureItem: React.FC<FeatureItemProps> = ({ icon, title, description }) => (
    <div className="bd-about-feature-item">
        <div className="bd-about-feature-icon">
            <span>
                <i className={icon}></i>
            </span>
        </div>
        <div className="bd-about-feature-content">
            <h6 className="bd-about-feature-title">{title}</h6>
            <p className="bd-about-feature-descrip">{description}</p>
        </div>
    </div>
);

// Define types for AboutImage props
interface AboutImageProps {
    src: StaticImageData;
    alt: string;
}

// Reusable AboutImage Component
const AboutImage: React.FC<AboutImageProps> = ({ src, alt }) => (
    <div className="bd-about-thumb-wrapper">
        <div className="bd-about-thumb">
            <Image src={src} style={{ width: "100%", height: "auto" }} alt={alt} />
        </div>
    </div>
);

// Main Component
const ModernSchoolingAboutUs: React.FC = () => {
    return (
        <section className="bd-about-area section-space theme-bg-05 fix">
            <div className="container">
                <div className="row align-items-center g-30">
                    {/* About Image */}
                    <div className="col-xl-5 col-lg-12">
                        <div className="bd-about-wrapper style-six">
                            <div className="bd-about-thumb-inner">
                                <AboutImage src={aboutThumb} alt="About Thumb" />
                            </div>
                        </div>
                    </div>

                    {/* About Content */}
                    <div className="col-xl-7 col-lg-12">
                        <div className="bd-about-wrapper style-six">
                            <div className="bd-about-content-wrapper">
                                <div className="bd-section-title-wrapper">
                                    <span className="bd-section-subtitle">About Us</span>
                                    <h2 className="bd-section-title mb-20">
                                        {`We're`} Focused on Technology and Innovative Learning
                                    </h2>
                                    <p className="bd-section-paragraph">
                                        Our mission is to empower students with the knowledge, skills, and mindset needed to succeed in the 21st century. We believe that education is more than just academics—it’s about nurturing well-rounded individuals who are confident.
                                    </p>
                                </div>
                                
                                {/* Features */}
                                <div className="bd-about-feature-wrapper">
                                    {featuresData.map((item) => (
                                        <FeatureItem 
                                            key={item.id}
                                            icon={item.icon}
                                            title={item.title}
                                            description={item.description}
                                        />
                                    ))}
                                </div>

                                {/* Know More Button */}
                                <div className="bd-about-btn">
                                    <Link className="bd-btn btn-outline-primary" href="/about-modern-schooling">
                                        Know More
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default ModernSchoolingAboutUs;
