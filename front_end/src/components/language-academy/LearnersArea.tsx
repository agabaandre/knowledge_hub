import Image, { StaticImageData } from 'next/image';
import Link from 'next/link';
import React from 'react';
import learnersBgImg from '../../../public/assets/images/bg/learners-bg.svg';
import LearnersBg1 from '../../../public/assets/images/learner/learners-bg-001.png';
import LearnersBg2 from '../../../public/assets/images/learner/learners-bg-002.png';
import LearnersBg3 from '../../../public/assets/images/learner/learners-bg-003.png';
import CountUpContent from '../common/counter/CountUpContent';

// Type for counter item
interface CounterItemProps {
    endValue: number;
    label: string;
    unit: string;
}

// Type for learner item
interface LearnerItemProps {
    imageSrc: StaticImageData;
    title: string;
    link: string;
    description: string;
}

// CounterItem Component
const CounterItem: React.FC<CounterItemProps> = ({ endValue, label, unit }) => (
    <div className="bd-learners-counter-item">
        <div className="bd-counter-item bd-counter-style-nine">
            <h2 className="bd-counter-total">
                <CountUpContent number={endValue} text={unit} />
            </h2>
            <p>{label}</p>
        </div>
    </div>
);

// LearnerItem Component
const LearnerItem: React.FC<LearnerItemProps> = ({ imageSrc, title, link, description }) => (
    <div className="single-item">
        <div className="thumb">
            <Image src={imageSrc} alt="learners" />
        </div>
        <div className="content">
            <h4 className="title underline-two">
                <Link href={link}>{title}</Link>
            </h4>
            <p className="intro">{description}</p>
        </div>
    </div>
);

const LearnersArea: React.FC = () => {
    return (
        <>
            {/* -- learners area start -- */}
            <section className="bd-learners-area section-space p-relative">
                <div className="bd-learners-bg" style={{ backgroundImage: `url(${learnersBgImg.src})` }}></div>
                <div className="container">
                    <div className="row gy-30 justify-content-between">
                        <div className="col-xl-6 col-lg-6 col-md-12">
                            <div className="bd-section-title-wrapper section-title-space">
                                <span className="bd-section-subtitle">Break Barriers, Speak Freely</span>
                                <h2 className="bd-section-title">Connect with the World Through Language</h2>
                            </div>
                            <div className="bd-learners-content">
                                <LearnerItem
                                    imageSrc={LearnersBg1}
                                    title="For Individuals"
                                    link="/pricing-table"
                                    description="Achieve your personal language goals with our interactive curriculum and user-friendly app. Learn at your own pace and unlock new opportunities."
                                />
                                <LearnerItem
                                    imageSrc={LearnersBg2}
                                    title="For Businesses"
                                    link="/pricing-table"
                                    description="Empower your team with language skills that drive global success. Our tailored solutions enhance communication and collaboration across borders."
                                />
                                <LearnerItem
                                    imageSrc={LearnersBg3}
                                    title="For Educators"
                                    link="/pricing-table"
                                    description="Transform your classroom with our engaging language programs. Inspire students to explore new cultures and become confident communicators."
                                />
                            </div>
                        </div>
                        <div className="col-xl-3 col-lg-3 col-md-12">
                            <div className="bd-learners-counter">
                                <CounterItem endValue={950} label="Courses" unit="+" />
                                <CounterItem endValue={50} label="Enrolled" unit="K+" />
                                <CounterItem endValue={250} label="Tutors" unit="+" />
                                <CounterItem endValue={10} label="Graduates" unit="M+" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- learners area end -- */}
        </>
    );
};

export default LearnersArea;
