import React from 'react';
import misssionImg from '../../../../../public/assets/images/mission/mission-vision-2.webp';
import misssionImgTwo from '../../../../../public/assets/images/mission/mission-vision-3.webp';
import misssionImgThree from '../../../../../public/assets/images/mission/mission-vision-4.webp';
import misssionImgFour from '../../../../../public/assets/images/mission/mission-vision-5.webp';
import Image from 'next/image';
import { MissionVisionData, MissionVisionImgData } from '@/interFace/interFace';


const missionVisionImgData: MissionVisionImgData[] = [
    {
        image: misssionImg,
    },
    {
        image: misssionImgTwo,
    },
    {
        image: misssionImgThree,
    },
    {
        image: misssionImgFour,
    },
];

const missionVisionData: MissionVisionData[] = [
    {
        title: "Our Vision",
        description: "iStudy will be and remain a center of excellence in higher education. It will gain recognition, nationally and globally and will attract students, faculty, and staff from all parts of the world.",
    },
    {
        title: "Our Mission",
        description: "iStudy will be and remain a center of excellence in higher education. It will gain recognition, nationally and globally and will attract students, faculty, and staff from all parts of the world.",
    },
    {
        title: "Our Strategy",
        description: "iStudy will be and remain a center of excellence in higher education. It will gain recognition, nationally and globally and will attract students, faculty, and staff from all parts of the world.",
    },
];

const MissionVisionArea = () => {
    return (
        <>
            <section className="bd-mvs-area section-space">
                <div className="container">
                    <div className="row gy-30 justify-content-center align-items-center section-title-space">
                        {missionVisionImgData.map((data, index) => (
                            <div className="col-md-3" key={index}>
                                <div className="bd-mvs-thumb">
                                    <Image src={data.image} style={{ height: 'auto', width: 'auto' }} alt="image" />
                                </div>
                            </div>
                        ))}
                    </div>
                    <div className="row gy-30">
                        {missionVisionData.map((data, index) => (
                            <div className="col-xl-4 col-lg-4" key={index}>
                                <div className="bd-mvs-box">
                                    <h4 className="title mb-15">{data.title}</h4>
                                    <p>{data.description}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
        </>
    );
};

export default MissionVisionArea;