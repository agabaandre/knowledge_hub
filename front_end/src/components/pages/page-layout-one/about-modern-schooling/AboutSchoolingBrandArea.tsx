import Image, { StaticImageData } from 'next/image';
import React from 'react';
import brandImgOne from '../../../../../public/assets/images/brand/brand-01.webp';
import brandImgTwo from '../../../../../public/assets/images/brand/brand-02.webp';
import brandImgThree from '../../../../../public/assets/images/brand/brand-03.webp';
import brandImgFour from '../../../../../public/assets/images/brand/brand-04.webp';
import brandImgFive from '../../../../../public/assets/images/brand/brand-05.webp';
import brandImgSix from '../../../../../public/assets/images/brand/brand-06.webp';

interface Brand {
    image: StaticImageData;
    alt: string;
}

const brandData: Brand[] = [
    { image: brandImgOne, alt: "Brand 1" },
    { image: brandImgTwo, alt: "Brand 2" },
    { image: brandImgThree, alt: "Brand 3" },
    { image: brandImgFour, alt: "Brand 4" },
    { image: brandImgFive, alt: "Brand 5" },
    { image: brandImgSix, alt: "Brand 6" },
    { image: brandImgOne, alt: "Brand 1" },
    { image: brandImgTwo, alt: "Brand 2" },
];

const AboutSchoolingBrandArea = () => {
    return (
        <>
            <section className="bd-brand-area section-space">
                <div className="container">
                    <div className="row">
                        <div className="col-xl-5 col-lg-5">
                            <div className="bd-brand-section-wrapper has-margin">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <span className="bd-section-subtitle">our partners</span>
                                    <h2 className="bd-section-title mb-20">Learn with Our Partners</h2>
                                    <p className="bd-section-paragraph">iStudy partners with more than 300 leading
                                        universities and companies to bring flexible, affordable, job-relevant online
                                        learning to individuals and organizations worldwide.</p>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-7 col-lg-7">
                            <div className="bd-brand-wrapper style-five">
                                <div className="bd-brand-box">
                                    {brandData.map((brand, index) => (
                                        <div className="bd-brand-item" key={index}>
                                            <Image src={brand.image} style={{ width: 'auto', height: 'auto' }} alt={brand.alt} />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutSchoolingBrandArea;