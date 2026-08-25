import Image from 'next/image';
import React from 'react';
import JourneyImg1 from '../../../../../public/assets/images/joining/journey-1.webp';
import JourneyImg2 from '../../../../../public/assets/images/joining/journey-2.webp';
import JourneyImg3 from '../../../../../public/assets/images/joining/journey-3.webp';

const JoiningInfoArea = () => {
    return (
        <>
            {/* -- joining info area start -- */}
            <section className="bd-joining-info-area section-space fix">
                <div className="container">
                    <div className="row g-60 justify-content-center">
                        <div className="col-xl-4 col-lg-6">
                            <div className="bd-section-wrapper section-title-space">
                                <h2 className="bd-section-title">How to Become an Instructor</h2>
                            </div>
                            <div className="tab-style-four">
                                <ul className="nav nav-pills" id="pills-tab" role="tablist">
                                    <li className="nav-item" role="presentation">
                                        <button className="nav-link active" id="pills-item-one-tab" data-bs-toggle="pill" data-bs-target="#pills-item-one" type="button" role="tab" aria-controls="pills-item-one" aria-selected="true">Quick Sign Up</button>
                                    </li>
                                    <li className="nav-item" role="presentation">
                                        <button className="nav-link" id="pills-item-two-tab" data-bs-toggle="pill" data-bs-target="#pills-item-two" type="button" role="tab" aria-controls="pills-item-two" aria-selected="false">Create Your
                                            Curriculum</button>
                                    </li>
                                    <li className="nav-item" role="presentation">
                                        <button className="nav-link" id="pills-item-three-tab" data-bs-toggle="pill" data-bs-target="#pills-item-three" type="button" role="tab" aria-controls="pills-item-three" aria-selected="false">Launch Your
                                            Course</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-xl-5 col-lg-6">
                            <div className="tab-style-four">
                                <div className="tab-content" id="pills-tabContent">
                                    <div className="tab-pane fade show active" id="pills-item-one" role="tabpanel" aria-labelledby="pills-item-one-tab" tabIndex={0}>
                                        <div className="bd-joining-box">
                                            <div className="bd-joining-thumb mb-30">
                                                <Image src={JourneyImg1} alt="image" />
                                            </div>
                                            <div className="bd-joining-content mb-30">
                                                <h6 className="title">01. Sign Up Quickly</h6>
                                                <p className="description">Register yourself as an instructor by filling out
                                                    a simple form and start sharing your knowledge with the world.</p>
                                            </div>
                                            <div className="bd-joining-content">
                                                <h6 className="title">02. Verify Your Profile</h6>
                                                <p className="description">Complete your profile verification process to
                                                    ensure trust and credibility within our learning community.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="tab-pane fade" id="pills-item-two" role="tabpanel" aria-labelledby="pills-item-two-tab" tabIndex={0}>
                                        <div className="bd-joining-box">
                                            <div className="bd-joining-thumb mb-30">
                                                <Image src={JourneyImg2} alt="image" />
                                            </div>
                                            <div className="bd-joining-content mb-30">
                                                <h6 className="title">01. Design Your Curriculum</h6>
                                                <p className="description">Plan and organize your course material
                                                    effectively. Use our tools to structure your curriculum and ensure
                                                    student engagement.</p>
                                            </div>
                                            <div className="bd-joining-content">
                                                <h6 className="title">02. Upload Course Materials</h6>
                                                <p className="description">Upload your lectures, assignments, and additional
                                                    resources. Make your content engaging and informative.</p>
                                            </div>

                                        </div>
                                    </div>
                                    <div className="tab-pane fade" id="pills-item-three" role="tabpanel" aria-labelledby="pills-item-three-tab" tabIndex={0}>
                                        <div className="bd-joining-box">
                                            <div className="bd-joining-thumb mb-30">
                                                <Image src={JourneyImg3} alt="image" />
                                            </div>
                                            <div className="bd-joining-content mb-30">
                                                <h6 className="title">01. Launch Your Course</h6>
                                                <p className="description">Once your course is ready, publish it and start
                                                    teaching students from around the globe.</p>
                                            </div>
                                            <div className="bd-joining-content">
                                                <h6 className="title">02. Interact with Students</h6>
                                                <p className="description">Engage with your students through discussions,
                                                    Q&A sessions, and feedback to improve the learning experience.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- joining info area start -- */}
        </>
    );
};

export default JoiningInfoArea;