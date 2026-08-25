"use client"
import Image from 'next/image';
import React from 'react';
import brand1 from '../../../public/assets/images/brand/brand-01.webp';
import brand2 from '../../../public/assets/images/brand/brand-02.webp';
import brand3 from '../../../public/assets/images/brand/brand-03.webp';
import brand4 from '../../../public/assets/images/brand/brand-04.webp';
import brand5 from '../../../public/assets/images/brand/brand-05.webp';
import brand6 from '../../../public/assets/images/brand/brand-06.webp';

const brandImg = [
    { src: brand1, alt: 'Partner 1' },
    { src: brand2, alt: 'Partner 2' },
    { src: brand3, alt: 'Partner 3' },
    { src: brand4, alt: 'Partner 4' },
    { src: brand5, alt: 'Partner 5' },
    { src: brand6, alt: 'Partner 6' }
];

const ModernSchoolingBrandArea = () => {


    return (
        <>
            {/* -- brand area start -- */}
            <div className="bd-brand-area pt-50 pb-50 theme-bg-05 fix">
                <div className="container">
                    <div className="row">
                        <div className="bd-brand-wrapper style-two">
                            <div className="bd-brand-box has-border">
                                {brandImg.map((image, index) => (
                                    <div className="bd-brand-item" key={index}>
                                        <Image
                                            style={{ width: "auto", height: "auto" }}
                                            src={image.src}
                                            alt={image.alt}
                                            priority
                                        />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- brand area end -- */}
        </>
    );
};

export default ModernSchoolingBrandArea;
