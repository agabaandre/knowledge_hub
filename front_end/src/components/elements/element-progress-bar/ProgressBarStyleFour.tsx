"use client";
import React, { useState, useEffect } from 'react';
import { CircularProgressbar } from 'react-circular-progressbar';


const ProgressBarStyleFour = () => {
    // State for progress values
    const [frontendProgress, setFrontendProgress] = useState(0);
    const [graphicProgress, setGraphicProgress] = useState(0);
    const [mobileProgress, setMobileProgress] = useState(0);
    const [softwareProgress, setSoftwareProgress] = useState(0);

    // Animation logic
    useEffect(() => {
        const interval = 10; // Update interval in milliseconds

        // Frontend Development Progress
        const frontendInterval = setInterval(() => {
            setFrontendProgress((prev) => {
                if (prev >= 80) {
                    clearInterval(frontendInterval);
                    return 80;
                }
                return prev + 1;
            });
        }, interval);

        // Graphic Design Progress
        const graphicInterval = setInterval(() => {
            setGraphicProgress((prev) => {
                if (prev >= 60) {
                    clearInterval(graphicInterval);
                    return 60;
                }
                return prev + 1;
            });
        }, interval);

        // Mobile Development Progress
        const mobileInterval = setInterval(() => {
            setMobileProgress((prev) => {
                if (prev >= 70) {
                    clearInterval(mobileInterval);
                    return 70;
                }
                return prev + 1;
            });
        }, interval);

        // Software Engineering Progress
        const softwareInterval = setInterval(() => {
            setSoftwareProgress((prev) => {
                if (prev >= 95) {
                    clearInterval(softwareInterval);
                    return 95;
                }
                return prev + 1;
            });
        }, interval);

        // Cleanup intervals on component unmount
        return () => {
            clearInterval(frontendInterval);
            clearInterval(graphicInterval);
            clearInterval(mobileInterval);
            clearInterval(softwareInterval);
        };
    }, []);

    return (
        <>
            {/* -- progress-bar style 04 start -- */}
            <section className="bd-elements-progress-bar section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Progress Bar Style 04</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        <div className="col-lg-3 col-md-6 col-sm-6 col-12">
                            {/* -- Start Single Progress Bar  -- */}
                            <div className="radial-progress-single">
                                <div className="radial-progress circle-text">
                                    <CircularProgressbar
                                        strokeWidth={6}
                                        value={frontendProgress}
                                        text={`${frontendProgress}%`}
                                        styles={{
                                            root: {
                                                width: '180px',
                                                height: '180px',
                                            },
                                            path: {
                                                stroke: '#07A169',
                                            },
                                            text: {
                                                fill: '#00170F',
                                                fontSize: '14px',
                                                fontWeight:600
                                            },
                                            trail: {
                                                stroke: '#C8C8C8',
                                            },
                                        }}
                                    />
                                </div>
                                <div className="circle-info">
                                    <h4 className="title">Frontend Development</h4>
                                    <h6 className="subtitle">Expert in building UI/UX</h6>
                                </div>
                            </div>
                            {/* -- End Single Progress Bar  -- */}
                        </div>
                        <div className="col-lg-3 col-md-6 col-sm-6 col-12">
                            {/* -- Start Single Progress Bar  -- */}
                            <div className="radial-progress-single">
                                <div className="radial-progress">
                                    <CircularProgressbar
                                        strokeWidth={6}
                                        value={graphicProgress}
                                        text={`${graphicProgress}%`}
                                        styles={{
                                            root: {
                                                width: '180px',
                                                height: '180px',
                                            },
                                            path: {
                                                stroke: '#32C8C2',
                                            },
                                            text: {
                                                fill: '#00170F',
                                                fontSize: '14px',
                                                fontWeight:600
                                            },
                                            trail: {
                                                stroke: '#C8C8C8',
                                            },
                                        }}
                                    />
                                </div>
                                <div className="circle-info">
                                    <h4 className="title">Graphic Design</h4>
                                    <h6 className="subtitle">Creative visual designs</h6>
                                </div>
                            </div>
                            {/* -- End Single Progress Bar  -- */}
                        </div>
                        <div className="col-lg-3 col-md-6 col-sm-6 col-12">
                            {/* -- Start Single Progress Bar  -- */}
                            <div className="radial-progress-single">
                                <div className="radial-progress">
                                    <CircularProgressbar
                                        strokeWidth={6}
                                        value={mobileProgress}
                                        text={`${mobileProgress}%`}
                                        styles={{
                                            root: {
                                                width: '180px',
                                                height: '180px',
                                            },
                                            path: {
                                                stroke: '#198754',
                                            },
                                              text: {
                                                fill: '#00170F',
                                                fontSize: '14px',
                                                fontWeight:600
                                            },
                                            trail: {
                                                stroke: '#C8C8C8',
                                            },
                                        }}
                                    />
                                </div>
                                <div className="circle-info">
                                    <h4 className="title">Mobile Development</h4>
                                    <h6 className="subtitle">Building mobile applications</h6>
                                </div>
                            </div>
                            {/* -- End Single Progress Bar  -- */}
                        </div>
                        <div className="col-lg-3 col-md-6 col-sm-6 col-12">
                            {/* -- Start Single Progress Bar  -- */}
                            <div className="radial-progress-single">
                                <div className="radial-progress">
                                    <CircularProgressbar
                                        strokeWidth={6}
                                        value={softwareProgress}
                                        text={`${softwareProgress}%`}
                                        styles={{
                                            root: {
                                                width: '180px',
                                                height: '180px',
                                            },
                                            path: {
                                                stroke: '#ffc107',
                                            },
                                            text: {
                                                fill: '#00170F',
                                                fontSize: '14px',
                                                fontWeight:600
                                            },
                                            trail: {
                                                stroke: '#C8C8C8',
                                            },
                                        }}
                                    />
                                </div>
                                <div className="circle-info">
                                    <h4 className="title">Software Engineering</h4>
                                    <h6 className="subtitle">Expert in software solutions</h6>
                                </div>
                            </div>
                            {/* -- End Single Progress Bar  -- */}
                        </div>
                    </div>
                </div>
            </section>
            {/* -- progress-bar style 04 end -- */}
        </>
    );
};

export default ProgressBarStyleFour;