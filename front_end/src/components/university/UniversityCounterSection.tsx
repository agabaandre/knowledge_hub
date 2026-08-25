import Image from 'next/image';
import React from 'react';
import CountUpContent from '../common/counter/CountUpContent';
import counterData from '@/data/counter-data';

import counterShapeSeven from '../../../public/assets/images/shape/counter-shape-7.webp';
import counterShapeEight from '../../../public/assets/images/shape/counter-shape-8.webp';
import counterShapeNine from '../../../public/assets/images/shape/counter-shape-9.webp';
import counterShapeTen from '../../../public/assets/images/shape/counter-shape-10.webp';
import counterShapeEleven from '../../../public/assets/images/shape/counter-shape-11.webp';
import counterShapeTwelve from '../../../public/assets/images/shape/counter-shape-12.webp';

const counterShapes = [
    counterShapeSeven,
    counterShapeEight,
    counterShapeNine,
    counterShapeTen,
    counterShapeNine,
    counterShapeEleven,
    counterShapeTwelve,
];

const UniversityCounterSection = () => (
    <section className="bd-counter-area section-space-bottom">
        <div className="container">
            <div className="p-relative fix">
                <div className="bd-counter-wrapper bd-counter-style-one theme-bg">
                    {counterData.slice(20, 24).map(({ id, counterNum, counterText }) => (
                        <div className="bd-counter-item" key={id}>
                            <div className="bd-counter-content">
                                <h2 className="bd-counter-title">
                                    <CountUpContent number={counterNum} text="+" />
                                </h2>
                                <p>{counterText}</p>
                            </div>
                        </div>
                    ))}
                </div>
                <div className="bd-counter-shape-wrap-two">
                    {counterShapes.map((shape, index) => (
                        <div key={index} className={`shape-${index + 1}`}>
                            <Image src={shape} style={{ width: '100%', height: 'auto' }} alt={`shape-${index + 1}`} />
                        </div>
                    ))}
                </div>
            </div>
        </div>
    </section>
);

export default UniversityCounterSection;
