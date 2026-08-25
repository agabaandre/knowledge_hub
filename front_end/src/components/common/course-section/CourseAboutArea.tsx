import React from "react";
import Image, { StaticImageData } from "next/image";
import Link from "next/link";
import circleImg from "../../../../public/assets/images/shape/about-solid-circle.webp";
import bookShapeImg from "../../../../public/assets/images/shape/about-book-shape.webp";
import aboutWaveShape from "../../../../public/assets/images/shape/about-wave-shape.webp";
import aboutCircle from "../../../../public/assets/images/shape/about-circle.webp";
import aboutThumb1 from "../../../../public/assets/images/about/about-thumb-01.webp";
import aboutThumbSmall from "../../../../public/assets/images/about/about-thumb-small-01.webp";
import dotShape from "../../../../public/assets/images/shape/dot-shape-01.webp";

interface AboutFeatureProps {
  iconClass: string;
  title: string;
  description: string;
}

const AboutFeatureItem: React.FC<AboutFeatureProps> = ({
  iconClass,
  title,
  description,
}) => (
  <div className="bd-about-feature-item">
    <div className="bd-about-feature-icon">
      <span>
        <i className={iconClass}></i>
      </span>
    </div>
    <div className="bd-about-feature-content">
      <h6 className="bd-about-feature-title">{title}</h6>
      <p className="bd-about-feature-desc">{description}</p>
    </div>
  </div>
);

interface AboutShapeProps {
  src: StaticImageData;
  alt: string;
  className: string;
}

const AboutShape: React.FC<AboutShapeProps> = ({ src, alt, className }) => (
  <div className={className}>
    <Image src={src} style={{ width: "100%", height: "auto" }} alt={alt} />
  </div>
);

const CourseAboutArea: React.FC = () => {
  return (
    <section className="bd-about-area section-space theme-bg p-relative bd-noise-bg">
      <div className="container">
        <div className="bd-about-shape-wrap">
          <AboutShape src={circleImg} alt="shape" className="shape-1" />
          <AboutShape src={bookShapeImg} alt="shape" className="shape-2" />
          <AboutShape src={aboutWaveShape} alt="shape" className="shape-3" />
          <AboutShape src={aboutCircle} alt="shape" className="shape-4" />
        </div>
        <div className="row g-5">
          <div className="col-xl-6 col-lg-6 col-md-12">
            <div className="bd-about-wrapper style-one">
              <div className="bd-about-thumb-inner">
                <div className="bd-about-thumb-wrapper">
                  <div className="bd-about-thumb">
                    <Image src={aboutThumb1} style={{ width: "100%", height: "auto" }} alt="image" priority />
                  </div>
                  <div className="bd-about-thumb-small">
                    <Image src={aboutThumbSmall} style={{ width: "100%", height: "auto" }} alt="image" priority />
                  </div>
                </div>
                <div className="bd-about-thumb-shape">
                  <Image src={dotShape} alt="image" />
                </div>
              </div>
            </div>
          </div>
          <div className="col-xl-6 col-lg-6 col-md-12">
            <div className="bd-about-wrapper style-one">
              <div className="bd-about-content-wrapper">
                <div className="bd-section-title-wrapper">
                  <span className="bd-section-subtitle text-secondary">About Us</span>
                  <h2 className="bd-section-title white-text mb-20">
                    A New Different Way To Improve Your <span className="down-mark-line">Skills</span>
                  </h2>
                  <p className="bd-section-paragraph has-border-sec">
                    Education is one of the most essential and valuable assets that an individual can possess. It plays a pivotal role in shaping.
                  </p>
                </div>
                <div className="bd-about-feature-list">
                  <AboutFeatureItem
                    iconClass="icon-online-class"
                    title="Flexible Classes"
                    description="The Flexible Classes feature allows students to customize their learning schedule."
                  />
                  <AboutFeatureItem
                    iconClass="icon-expert-trainers"
                    title="Expert Trainers"
                    description="All trainers hold relevant certifications and advanced degrees, guaranteeing that you are learning from qualified experts."
                  />
                  <AboutFeatureItem
                    iconClass="icon-career-growth"
                    title="Build Your Career"
                    description="Prepare for job interviews with mock interview sessions, feedback, and tips on how to present yourself confidently."
                  />
                </div>
                <div className="bd-about-btn">
                  <Link className="bd-btn btn-secondary-white" href="/about-online-course">
                    Know More
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default CourseAboutArea;
