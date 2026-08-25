import { schoolingWhyChooseData } from '@/data/why-choose-data';
import React from 'react';

const SchoolingWhyChooseAreaCommon = () => {
    return (
        <>
            {/* -- why choose area start -- */}
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Why iStudy?</span>
                                <h2 className="bd-section-title mb-20">Explore Our Unique Offerings</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row g-30">
                        {
                            schoolingWhyChooseData.map((item) => (
                                <div className="col-xl-4 col-lg-4 col-md-6" key={item.id}>
                                    <div className="bd-why-choose-wrapper style-two wow bdFadeInUp" data-wow-delay={`${item.wowDelayDuration}`}>
                                        <div className="bd-why-choose-item bg-flashlight">
                                            <span className="bd-why-choose-icon">
                                                <item.icon />
                                            </span>
                                            <div className="bd-why-choose-content">
                                                <h5 className="bd-why-choose-title">{item.title}</h5>
                                                <p className="bd-why-choose-description">{item.description}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        }
                    </div>
                </div>
            {/* -- why choose area end -- */}
        </>
    );
};

export default SchoolingWhyChooseAreaCommon;