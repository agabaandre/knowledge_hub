import DeviceSvg from '@/svg/DeviceSvg';
import LearnCourseSvg from '@/svg/LearnCourseSvg';
import VideoCourseSvg from '@/svg/VideoCourseSvg';
import React from 'react';

const LanguageAcademyFeatureArea = () => {
    return (
        <>
            {/* -- feature area start -- */}
            <div className="bd-feature-area theme-bg pt-40 pb-40 bd-noise-bg">
                <div className="container">
                    <div className="row gy-30 justify-content-between align-items-center">
                        <div className="col-xl-4 col-lg-4 col-md-12">
                            <div className="bd-feature-wrapper style-one wow bdFadeInUp">
                                <div className="bd-feature-item">
                                    <span className="icon">
                                        <VideoCourseSvg />
                                    </span>
                                    <p className="description">Master new languages with access to over 24,000 interactive lessons and
                                        courses.</p>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-4 col-lg-4 col-md-12">
                            <div className="bd-feature-wrapper style-one wow bdFadeInUp">
                                <div className="bd-feature-item">
                                    <span className="icon">
                                        <LearnCourseSvg />
                                    </span>
                                    <p className="description">Learn from certified language instructors and native speakers with
                                        real-world expertise.</p>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-4 col-lg-4 col-md-12">
                            <div className="bd-feature-wrapper style-one wow bdFadeInUp">
                                <div className="bd-feature-item">
                                    <span className="icon">
                                        <DeviceSvg />
                                    </span>
                                    <p className="description">Study at your own pace, anytime and anywhere, with unlimited access on
                                        all your devices.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- feature area end -- */}
        </>
    );
};

export default LanguageAcademyFeatureArea;