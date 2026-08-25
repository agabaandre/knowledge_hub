import Image from 'next/image';
import React from 'react';
import { CoreValue } from "@/interFace/interFace";
import bgCoreValue from '../../../../../public/assets/images/mission/core-values.webp';
import handshakeIcon from '../../../../../public/assets/images/icon/handshake.svg';
import innovationIcon from '../../../../../public/assets/images/icon/innovation.svg';
import excellenceIcon from '../../../../../public/assets/images/icon/excellence.svg';
import textBookIcon from '../../../../../public/assets/images/icon/text-books.svg';

export const coreValuesData: CoreValue[] = [
    {
        icon: handshakeIcon,
        title: "Integrity",
        description: "We uphold the highest standards of honesty and ethical behavior in all our interactions, ensuring that our students and staff thrive in a respectful and trustworthy environment."
    },
    {
        icon: innovationIcon,
        title: "Innovation",
        description: "We embrace creativity and innovation, constantly seeking new ways to enhance our educational practices and stay at the forefront of modern learning techniques."
    },
    {
        icon: excellenceIcon,
        title: "Excellence",
        description: "We are dedicated to achieving excellence in all aspects of our educational programs, striving for continuous improvement and excellence in teaching and learning."
    },
    {
        icon: textBookIcon,
        title: "Our Educational Philosophy",
        description: "At <strong>iStudy</strong>, we believe in a student-centered approach that nurtures individual strengths and fosters a passion for lifelong learning. Our philosophy centers on creating an engaging, supportive, and inclusive environment where every student has the opportunity to excel and contribute meaningfully to the world."
    }
];

const AboutSchoolingCoreValueArea = () => {

    return (
        <>
            <section className="bd-core-values-area bd-core-values-bg section-space" style={{backgroundImage: `url(${bgCoreValue.src})`}}>
                <div className="container">
                    <div className="row justify-content-between">
                        <div className="col-xl-6">
                            <div className="bd-section-wrapper section-title-space sidebar-sticky">
                                <h2 className="bd-section-title white-text mb-20">Core Values & Philosophy</h2>
                                <p className="bd-section-paragraph white-text">The Principles That Drive Our Educational Approach at iStudy</p>
                            </div>
                        </div>
                        <div className="col-xl-5">
                            <div className="bd-core-values-box-wrapper">
                                {coreValuesData.map((value, index) => (
                                    <div className="bd-core-values-box bg-flashlight" key={index}>
                                        <div className="bd-core-values-icon">
                                            <Image src={value.icon} style={{width: 'auto', height: 'auto'}} alt="icon"/>
                                        </div>
                                        <h4 className="bd-core-values-title">{value.title}</h4>
                                        <p className="bd-core-values-desc" dangerouslySetInnerHTML={{ __html: value.description }} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutSchoolingCoreValueArea;