import React from 'react';
import counterShape1 from "../../../../public/assets/images/shape/counter-shape-1.webp";
import counterShape2 from "../../../../public/assets/images/shape/counter-shape-2.webp";
import counterShape3 from "../../../../public/assets/images/shape/counter-shape-3.webp";
import counterShape4 from "../../../../public/assets/images/shape/counter-shape-4.webp";
import counterShape5 from "../../../../public/assets/images/shape/counter-shape-5.webp";
import counterShape6 from "../../../../public/assets/images/shape/counter-shape-6.webp";
import Image from 'next/image';
import counterData from '@/data/counter-data';
import CountUpContent from '../counter/CountUpContent';

const CourseCounterArea = () => {
    return (
        <>
            {/* -- counter area start -- */}
            <div className="bd-counter-area bd-counter-over-top p-relative section-space-bottom">
                <div className="container">
                    <div className="bd-counter-wrapper bd-counter-border">
                        <div className="p-relative fix">
                            <div className="bd-counter-style-one">
                                <div className="bd-counter-bg-2 theme-bg bd-noise-bg"></div>
                                {
                                    counterData.slice(0, 4).map((item) => (
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
                            <div className="bd-counter-shape-wrap">
                                <div className="shape-1"><Image src={counterShape1} alt="shape" /></div>
                                <div className="shape-2"><Image src={counterShape2} style={{ width: "100%", height: "auto" }} alt="shape" /></div>
                                <div className="shape-3"><Image src={counterShape6} style={{ width: "100%", height: "auto" }} alt="shape" /></div>
                                <div className="shape-4"><Image src={counterShape4} style={{ width: "100%", height: "auto" }} alt="shape" /></div>
                                <div className="shape-5"><Image src={counterShape5} alt="shape" /></div>
                                <div className="shape-6"><Image src={counterShape3} style={{ width: "100%", height: "auto" }} alt="shape" /></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- counter area end -- */}
        </>
    );
};

export default CourseCounterArea;