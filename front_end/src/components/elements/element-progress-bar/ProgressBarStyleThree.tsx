import React from 'react';

const ProgressBarStyleThree = () => {
    return (
        <>
            {/* -- progress-bar style 03 start -- */}
            <section className="bd-elements-progress-bar section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Progress Bar Style 03</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30 justify-content-center">
                        <div className="col-xl-8">
                            <div className="bd-progress-wrapper progress-style-3">
                                <div className="single-progress wow bdFadeInLeft" data-wow-duration="0.5s" data-wow-delay=".3s">
                                    <h6 className="title">Frontend Development</h6>
                                    <div className="progress">
                                        <div className="progress-bar"></div>
                                        <span className="progress-number">90%</span>
                                    </div>
                                </div>
                                <div className="single-progress bg-gradient-2 wow bdFadeInLeft" data-wow-duration="0.5s" data-wow-delay=".3s">
                                    <h6 className="title">Graphic Design</h6>
                                    <div className="progress">
                                        <div className="progress-bar"></div>
                                        <span className="progress-number">75%</span>
                                    </div>
                                </div>
                                <div className="single-progress bg-gradient-3 wow bdFadeInLeft" data-wow-duration="0.5s" data-wow-delay=".3s">
                                    <h6 className="title">Mobile Development</h6>
                                    <div className="progress">
                                        <div className="progress-bar"></div>
                                        <span className="progress-number">95%</span>
                                    </div>
                                </div>
                                <div className="single-progress bg-gradient-4 wow bdFadeInLeft" data-wow-duration="0.5s" data-wow-delay=".3s">
                                    <h6 className="title">Software Engineering</h6>
                                    <div className="progress">
                                        <div className="progress-bar"></div>
                                        <span className="progress-number">45%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- progress-bar style 03 end -- */}
        </>
    );
};

export default ProgressBarStyleThree;