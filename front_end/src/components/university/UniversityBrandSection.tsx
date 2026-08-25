import React from 'react';
import brand1 from '../../../public/assets/images/brand/brand-01.webp';
import brand2 from '../../../public/assets/images/brand/brand-02.webp';
import brand3 from '../../../public/assets/images/brand/brand-03.webp';
import brand4 from '../../../public/assets/images/brand/brand-04.webp';
import brand5 from '../../../public/assets/images/brand/brand-05.webp';
import brand6 from '../../../public/assets/images/brand/brand-06.webp';
import Image from 'next/image';

const partners = [
    { src: brand1, alt: 'Partner 1' },
    { src: brand2, alt: 'Partner 2' },
    { src: brand3, alt: 'Partner 3' },
    { src: brand4, alt: 'Partner 4' },
    { src: brand5, alt: 'Partner 5' },
    { src: brand6, alt: 'Partner 6' },
    { src: brand1, alt: 'Partner 7' },
    { src: brand2, alt: 'Partner 8' },
];

const UniversityBrandSection = () => {
    return (
        <>
            {/* -- brand area start -- */}
            <section className="bd-brand-area section-space primary-bg">
                <div className="container">
                    <div className="row">
                        <div className="col-xl-5 col-lg-5">
                            <div className="bd-brand-section-wrapper has-margin">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <span className="bd-section-subtitle">our partners</span>
                                    <h2 className="bd-section-title mb-20">Learn with Our <span
                                        className="down-mark-line">Partners</span></h2>
                                    <p className="bd-section-paragraph"> Outline how the university benefits from the
                                        partnership, such as increased funding, access to expertise</p>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-7 col-lg-7">
                            <div className="bd-brand-wrapper style-five">
                                <div className="bd-brand-box">

                                    {partners.map((partner, index) => (
                                        <div key={index} className="bd-brand-item">
                                            <Image
                                                src={partner.src}
                                                alt={partner.alt}
                                                style={{width:"auto", height:"auto"}}
                                            />
                                        </div>
                                    ))}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- brand area end -- */}
        </>
    );
};

export default UniversityBrandSection;