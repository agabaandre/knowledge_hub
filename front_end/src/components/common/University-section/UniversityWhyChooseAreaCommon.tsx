
import Image from 'next/image';
import React from 'react';
import whyChooseBg from '../../../../public/assets/images/bg/university.webp';
import { whyChooseData } from '@/data/why-choose-data';


const UniversityWhyChooseAreaCommon = () => {
    return (
        <section className="bd-why-choose-area primary-bg section-space image-bg" style={{ backgroundImage: `url(${whyChooseBg.src})` }}>
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-6">
                        <div className="bd-section-wrapper section-title-space text-center">
                            <span className="bd-section-subtitle">Why Choose Us</span>
                            <h2 className="bd-section-title mb-20">Why Choose <span className="down-mark-line">iStudy</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="row g-30">
                    {
                        whyChooseData.length > 0 ? (
                            whyChooseData.map((item) =>
                                <div key={item.id} className="col-xl-3 col-lg-6 col-md-6">
                                    <div className="bd-why-choose-wrapper style-three">
                                        <div className="bd-why-choose-item">
                                            <span className="bd-why-choose-icon"><Image src={item.icon} style={{ width: '100%', height: 'auto' }} alt="icon" /></span>
                                            <div className="bd-why-choose-content">
                                                <h5 className="bd-why-choose-title mb-15">{item.title}</h5>
                                                <p className="bd-why-choose-description">{item.description}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )
                        ) : ''
                    }
                </div>
            </div>
        </section>
    );
};

export default UniversityWhyChooseAreaCommon;