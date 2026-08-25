import Image from 'next/image';
import React from 'react';
import counterShapeSeven from '../../../../../public/assets/images/shape/counter-shape-7.webp';
import counterShapeEight from '../../../../../public/assets/images/shape/counter-shape-8.webp';
import counterShapeNine from '../../../../../public/assets/images/shape/counter-shape-9.webp';
import counterShapeTen from '../../../../../public/assets/images/shape/counter-shape-10.webp';
import counterShapeEleven from '../../../../../public/assets/images/shape/counter-shape-11.webp';
import counterShapeTwelve from '../../../../../public/assets/images/shape/counter-shape-12.webp';
import counterData from '@/data/counter-data';
import CountUpContent from '@/components/common/counter/CountUpContent';

const AboutUniversityCounterArea = () => {
    return (
        <>
            <div className="bd-counter-area section-space">
                <div className="container">
                    <div className="p-relative fix">
                        <div className="bd-counter-wrapper bd-counter-style-one border-none theme-bg">
                            {
                                counterData.slice(8, 12).map((item) => (
                                    <div className="bd-counter-item" key={item.id}>
                                        <div className="bd-counter-content">
                                            <h2 className="bd-counter-title">
                                                <CountUpContent
                                                    number={item.counterNum}
                                                    text="+"
                                                />
                                            </h2>
                                            <p>{item.counterText}</p>
                                        </div>
                                    </div>
                                ))
                            }
                        </div>
                        <div className="bd-counter-shape-wrap-two">
                            <div className="shape-1"><Image src={counterShapeSeven} style={{width: '100%',  height: 'auto',}} alt="shape" /></div>
                            <div className="shape-2"><Image src={counterShapeEight} style={{width: '100%', height: 'auto' }} alt="shape" /></div>
                            <div className="shape-3"><Image src={counterShapeNine} style={{width: '100%', height: 'auto'}} alt="shape" /></div>
                            <div className="shape-4"><Image src={counterShapeTen} style={{width: '100%', height: 'auto'}} alt="shape" /></div>
                            <div className="shape-5"><Image src={counterShapeNine} style={{width: '100%', height: 'auto'}} alt="shape" /></div>
                            <div className="shape-6"><Image src={counterShapeEleven} style={{width: '100%', height: 'auto'}} alt="shape" /></div>
                            <div className="shape-7"><Image src={counterShapeTwelve} style={{width: '100%', height: 'auto'}} alt="shape" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default AboutUniversityCounterArea;