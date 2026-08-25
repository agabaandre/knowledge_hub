import CalanderSvg from '@/svg/why-choose-svgs/CalanderSvg';
import FacultySvg from '@/svg/why-choose-svgs/FacultySvg';
import GraduationCapIcon from '@/svg/why-choose-svgs/GraduationCapIcon';
import IndustryConnectionSvg from '@/svg/why-choose-svgs/IndustryConnectionSvg';
import Image from 'next/image';
import React from 'react';
import chooseImage from "../../../public/assets/images/choose/why-choose-quran.webp";

interface ChooseOption {
    id: number;
    Icon: React.FC;
    title: string;
    description: string;
}

const chooseOptions: ChooseOption[] = [
    {
        id: 1,
        Icon: GraduationCapIcon,
        title: "Qualified Quran Instructors",
        description: "Our Teachers bring a wealth of knowledge and dedication to provide high-quality, authentic learning experiences."
    },
    {
        id: 2,
        Icon: FacultySvg,
        title: "Engaging Online Classes",
        description: "Participate in live, interactive classes designed to strengthen understanding and offer real-time feedback."
    },
    {
        id: 3,
        Icon: CalanderSvg,
        title: "Flexible Class Scheduling",
        description: "Learn at your own pace with convenient class times designed to fit any schedule or time zone."
    },
    {
        id: 4,
        Icon: IndustryConnectionSvg,
        title: "Learning Community",
        description: "Become part of a community that fosters support, guidance, and shared growth on your Quranic journey."
    }
];

const QuranChooseArea: React.FC = () => {
    return (
        <section className="bd-why-choose-area section-space theme-bg bd-noise-bg">
            <div className="container">
                <div className="row gy-30 align-items-center justify-content-between">
                    <div className="col-xl-6 col-lg-6">
                        <div className="bd-section-wrapper section-title-space text-cccenter">
                            <span className="bd-section-subtitle text-white">Our Unique Approach</span>
                            <h2 className="bd-section-title text-white">Why Choose Us?</h2>
                        </div>
                        <div className="bd-choose-thumb-mask">
                            <div className="bd-choose-thumb">
                                <Image src={chooseImage} style={{width:"100%", height:"auto"}} alt="Why Choose Us" />
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6 col-lg-6">
                        <div className="bd-why-choose-grid">
                            {chooseOptions.map(({ id, Icon, title, description }) => (
                                <div key={id} className="bd-why-choose-wrapper style-four">
                                    <div className="bd-why-choose-item">
                                        <div className="bd-why-choose-icon-wrapper">
                                            <span className="bd-why-choose-icon">
                                                <Icon />
                                            </span>
                                        </div>
                                        <div className="bd-why-choose-content">
                                            <h5 className="bd-why-choose-title mb-15">{title}</h5>
                                            <p className="bd-why-choose-description">{description}</p>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default QuranChooseArea;
