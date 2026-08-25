import Link from 'next/link';
import React from 'react';
import careerCtaThumb from "../../../public/assets/images/cta/career-cta-thumb-01.webp";
import  careerCtaBg from "../../../public/assets/images/cta/career-cta-bg-01.webp";
import careerCtaBg2 from "../../../public/assets/images/cta/career-cta-bg-02.webp";
import careerCtaThumb2 from "../../../public/assets/images/cta/career-cta-thumb-02.webp";
import Image from 'next/image';

// Data array for mapping
const careerData = [
    {
        id: 1,
        bgImage: careerCtaBg,
        thumbImage: careerCtaThumb,
        subtitle: "Start Your Journey Today",
        subtitleClass: "text-secondary",
        title: "Become an Instructor & Share Your Expertise",
        link: "/become-instructor",
        btnClass: "btn-secondary",
        btnText: "Learn More",
    },
    {
        id: 2,
        bgImage: careerCtaBg2,
        thumbImage: careerCtaThumb2,
        subtitle: "Unlock Your Potential",
        subtitleClass: "text-primary",
        title: "Enhance Your Skills and Stay Ahead",
        link: "/courses",
        btnClass: "btn-primary",
        btnText: "Explore Courses",
    },
];

const CourseCareerArea = () => {
    return (
        <>
            {/* -- career area start -- */}
            <section className="bd-career-area bd-career-overlay">
                <div className="container">
                    <div className="row gy-30 justify-content-center">
                        <div className="col-xxl-12 col-xl-12 col-lg-12">
                            <div className="bd-career-grid">
                                {careerData.map((item) => (

                                    <div key={item.id} className="bd-career-wrapper style-one">
                                        <div className="bd-career-item">
                                            <div className="bd-career-bg">
                                                <Image src={item.bgImage} priority alt="background" />
                                            </div>
                                            <div className="bd-career-thumb">
                                                <Image src={item.thumbImage} priority alt="thumbnail" />
                                            </div>
                                            <div className="bd-career-content">
                                                <span className={`bd-career-subtitle ${item.subtitleClass}`}>
                                                    {item.subtitle}
                                                </span>
                                                <h4 className="bd-career-title underline">
                                                    <Link href="#">{item.title}</Link>
                                                </h4>
                                                <div className="bd-career-btn">
                                                    <Link className={`bd-btn ${item.btnClass} btn-small`} href={item.link}>
                                                        {item.btnText}
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- career area end -- */}
        </>
    );
};

export default CourseCareerArea;
