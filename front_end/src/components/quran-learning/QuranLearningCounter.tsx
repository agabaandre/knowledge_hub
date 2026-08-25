import React from "react";
import Image from "next/image";
import CounterMask from "../../../public/assets/images/shape/counter-mask.webp";
import CountUpContent from "../common/counter/CountUpContent";

const counters = [
    { label: "Enrolled Students", counterNum: 1200 },
    { label: "Experienced Instructors", counterNum: 250 },
    { label: "Courses Offered", counterNum: 313 },
    { label: "Community Members", counterNum: 5000 },
];

const QuranLearningCounter = () => {
    return (
        <div className="bd-counter-area section-space-medium theme-bg bd-noise-bg">
            <div className="container">
                <div className="row gy-30">
                    {counters.map((counter, index) => (
                        <div key={index} className="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div className="bd-counter-style-eight">
                                <div className="thumb">
                                    <Image src={CounterMask} alt="shape" />
                                </div>
                                <div className="bd-counter-text">
                                    <h3 className="bd-counter-text-title">
                                        <CountUpContent
                                            number={counter.counterNum}
                                            text="+"
                                        />
                                    </h3>
                                    <p>{counter.label}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default QuranLearningCounter;
