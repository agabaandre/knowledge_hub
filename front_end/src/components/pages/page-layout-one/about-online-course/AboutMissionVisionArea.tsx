import Image from 'next/image';
import React from 'react';
import gradientCircleShape from '../../../../../public/assets/images/shape/gradient-circle.webp';
import { missionVisionData } from '@/data/mission-vision-data';

const AboutMissionVisionArea = () => {
    return (
        <>
            <section className="bd-mission-vision section-space">
                <div className="container">
                    <div className="row gy-30">
                        {
                            missionVisionData.length > 0 &&
                            missionVisionData.map((item, index) => <div key={index} className="col-xl-4 col-lg-4 col-md-12">
                                <div className="bd-mission-vision-box">
                                    <div className="bd-mission-vision-shape"><Image src={gradientCircleShape} style={{ width: '100%', height: '100%' }} alt="shape" /></div>
                                    <span className="bd-mission-vision-icon"><Image src={item.img} style={{ width: '64px', height: '64px' }} alt="icon" /></span>
                                    <h3 className="bd-mission-vision-title">{item.title}</h3>
                                    <p className="bd-mission-vision-description">{item.description}</p>
                                </div>
                            </div>)
                        }
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutMissionVisionArea;