import Image from 'next/image';
import React from 'react';
import modernMissionImg from '../../../../../public/assets/images/mission/modern-mission.webp';
import counterData from '@/data/counter-data';
import CountUpContent from '@/components/common/counter/CountUpContent';

const AboutSchoolingMissionArea = () => {
    return (
        <>
            <section className="bd-why-choose-area section-space" id="learnMore">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Our Mission & Vision</span>
                                <h2 className="bd-section-title mb-20">Guiding the Future of Education at <span
                                    className="text-primary">iStudy</span></h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30 align-items-center justify-content-between">
                        <div className="col-xl-5">
                            <div className="bd-mission-vision-thumb">
                                <div className="bd-mission-vision-thumb-mask">
                                    <Image src={modernMissionImg} style={{width: 'auto', height: 'auto'}} alt="image" />
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-7">
                            <div className="bd-mission-vision-wrapper mb-20">
                                <h3 className="bd-mission-vision-title">Our Mission</h3>
                                <p className="bd-mission-vision-desc">At <strong>iStudy</strong>, our mission is to
                                    empower students with the knowledge and skills they need to thrive in a rapidly
                                    changing world. We are committed to fostering a learning environment that
                                    encourages curiosity, innovation, and excellence.</p>
                            </div>
                            <div className="bd-mission-vision-wrapper">
                                <h3 className="bd-mission-vision-title">Our Vision</h3>
                                <p className="bd-mission-vision-desc">We envision <strong>iStudy</strong> as a global
                                    leader in education, shaping the next generation of thinkers, creators, and leaders. Our
                                    goal is to offer cutting-edge learning experiences that inspire students to achieve
                                    their full potential and make a positive impact on society.</p>
                            </div>
                        </div>
                    </div>
                    <div className="bd-counter-wrapper bd-counter-style-five has-transparent pt-50">
                        {
                            counterData.slice(12, 16).map((item) => (
                                <div className="bd-counter-item" key={item.id}>
                                    <div className="bd-counter-content">
                                        <h2 className="bd-counter-title">
                                            <CountUpContent
                                                number={item.counterNum}
                                                text={item.suffix}
                                            />
                                        </h2>
                                        <p>{item.counterText}</p>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutSchoolingMissionArea;