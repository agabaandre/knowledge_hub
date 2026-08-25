import React from 'react';
import Image from 'next/image';
import { homefeaturesData } from '@/data/feature-data';

const FeatureArea = () => {
    return (
        <>
            {/* -- Start Feature Area  -- */}
            <div className="feature-area landing-feature section-space-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-7 col-lg-7">
                            <div className="section-title-wrapper text-center section-title-space">
                                <span className="bd-section-subtitle">EXPLORE OUR TOP FEATURES</span>
                                <h2 className="bd-section-title">Unlock Exceptional Features</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        <div className="col-12">
                            <div className="features-wrapper">
                                {homefeaturesData.map((item, index) => (
                                    <div key={index} className="features-item wow bdFadeInUp" data-wow-delay={item.wowDelay}>
                                        <div className="features-content">
                                            <div className={`features-icon ${item.tagClass}`}>
                                                <Image src={item.icon} style={{width:"100%", height:"auto"}} alt={item.title} />
                                            </div>
                                            <h4 className="features-title">{item.title}</h4>
                                            <p>{item.description}</p>
                                        </div>
                                        <div className={`features-tag ${item.tagClass}`}>
                                            <span>{item.tag}</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- End Feature Area  -- */}
        </>
    );
};

export default FeatureArea;
