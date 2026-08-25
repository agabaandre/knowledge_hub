"use client"

import React from 'react';
import videoBg from "../../../public/assets/images/bg/video-bg-01.webp";
import { Parallax } from 'react-parallax';
import { useVideoModal } from '@/contextApi/VideoProvider';

const UniversityVideoSection = () => {
     const { playVideo } = useVideoModal();
    return (
        <>
            <Parallax
                bgImage={videoBg.src}
                strength={400}
            >
                <div className="bd-video-area p-relative section-space style-one">
                    <div className="video-bg-thumb include-bg bg-attach-fix" style={{ backgroundImage: `url(${videoBg.src})` }}>
                    </div>
                    <div className="container">
                        <div className="row align-items-center justify-content-center g-30">
                            <div className="col-xl-6 col-lg-8">
                                <div className="bd-video-wrapper">
                                    <div className="bd-video-content">
                                        <div className="bd-section-title-wrapper">
                                            <h2 className="bd-section-title">Here <span className="text-secondary">University</span>{" "}
                                                Video intro that focuses on Learning The <span
                                                    className="text-secondary">Campus</span>
                                            </h2>
                                        </div>
                                        <p>Our community is being called to remain the future. As the only university where a
                                            renowned design school colleges</p>
                                    </div>
                                </div>
                            </div>
                            <div className="col-xl-6 col-lg-4">
                                <div className="bd-video-play-btn">
                                    <button  type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn popup-video has-bg">
                                        <span className="icon"><i className="fa-solid fa-play"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Parallax>
            {/* -- video area start -- */}
        </>
    );
};

export default UniversityVideoSection;
