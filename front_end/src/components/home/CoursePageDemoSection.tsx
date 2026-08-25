import Link from 'next/link';
import React from 'react';
import thumb1 from '../../../public/assets/images/landing-page/course/course-demo-thumb-1.webp';
import thumb2 from '../../../public/assets/images/landing-page/course/course-demo-thumb-2.webp';
import thumb3 from '../../../public/assets/images/landing-page/course/course-demo-thumb-3.webp';
import thumb4 from '../../../public/assets/images/landing-page/course/course-demo-thumb-4.webp';
import Image from 'next/image';

const CoursePageDemo = () => {
    return (
        <>
            {/* -- Course Page Demo Area Start -- */}
            <section className="demo-course-area theme-bg bd-noise-bg section-space">
                <div className="container">
                    <div className="row gy-30 align-items-center justify-content-between">
                        <div className="col-xl-5 col-lg-5">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle text-secondary">Explore Our Courses</span>
                                <h2 className="bd-section-title white-text mb-20">A Smarter Way to Elevate Your Knowledge</h2>
                                <p className="bd-section-paragraph white-text-7">
                                    Education unlocks the door to endless possibilities. Discover our expertly crafted courses
                                    designed to enhance your learning journey and help you achieve your goals.
                                </p>
                                <div className="demo-course-btn">
                                    <Link className="bd-btn btn-secondary-white" href="/courses">Browse Courses</Link>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-7 col-lg-7">
                            <div className="demo-course-thumb-wrap text-lg-end text-center">
                                <div className="demo-course-thumb-main">
                                    <Image src={thumb1} style={{width:"auto", height:"auto"}} alt="course-demo" />
                                </div>
                                <div className="demo-course-thumb-1 d-none d-xl-block">
                                    <Image src={thumb2} alt="course-demo" />
                                </div>
                                <div className="demo-course-thumb-2 d-none d-xl-block">
                                    <Image src={thumb3} alt="course-demo" />
                                </div>
                                <div className="demo-course-thumb-3 d-none d-xl-block">
                                    <Image src={thumb4} alt="course-demo" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- Course Page Demo Area End -- */}
        </>
    );
};

export default CoursePageDemo;