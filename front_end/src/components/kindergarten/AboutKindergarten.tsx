"use client"
import Image from 'next/image';
import React from 'react';
import AboutThumb from '../../../public/assets/images/about/about-thumb-05.webp';
import AboutThumbSmall from '../../../public/assets/images/about/about-thumb-small-02.webp';
import textShape from '../../../public/assets/images/shape/text-shape.webp';
import waveShape from '../../../public/assets/images/shape/wave-shape.webp';
import orangeShape from '../../../public/assets/images/shape/orange-shape.webp';
import Link from 'next/link';
import MouseMoveEffect from '../common/MouseMoveEffect';
import { useVideoModal } from '@/contextApi/VideoProvider';

const AboutKindergarten = () => {
  // Call the custom hook here
  const { playVideo } = useVideoModal();
  MouseMoveEffect();

  const promotionData = [
    { number: '14+', text: ['Years of', 'experience'] },
    { number: '7K+', text: ['Students', 'each year'] },
    { number: '15+', text: ['Award', 'Wining'] },
  ];

  const features = [
    "We believe every child is intelligent so we care.",
    "Teachers make a difference for your child.",
  ];

  return (
    <>
      {/* -- about area start -- */}
      <section className="bd-about-area theme-bg-05 section-space">
        <div className="container">
          <div className="row g-30 align-items-center">
            <div className="col-xl-6 col-lg-6 col-md-12">
              <div className="bd-about-wrapper style-four">
                <div className="bd-about-thumb-inner">
                  <div className="bd-about-thumb-wrapper">
                    <div className="bd-about-thumb has-radius">
                      <Image src={AboutThumb} style={{ width: "auto", height: "auto" }} alt="About Image" />
                    </div>
                    <div className="bd-about-thumb has-radius has-small">
                      <Image style={{ width: "100%", height: "auto" }} src={AboutThumbSmall} alt="Small Image" />
                    </div>
                    <div className="bd-about-shape-wrapper d-none d-md-block" />
                    <div className="bd-about-shape has-shape-one">
                      <Image src={textShape} alt="Text Shape" />
                    </div>
                    <div className="bd-about-shape has-shape-two shape-move">
                      <Image style={{ width: "100%", height: "auto" }} src={waveShape} alt="Wave Shape" />
                    </div>
                    <div className="bd-about-shape has-shape-three d-none d-xl-block">
                      <Image style={{ width: "100%", height: "auto" }} src={orangeShape} alt="Orange Shape" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-xl-6 col-lg-6 col-md-12">
              <div className="bd-about-content-wrapper">
                <div className="bd-section-title-wrapper section-title-space">
                  <span className="bd-section-subtitle">About Kindergarten</span>
                  <h2 className="bd-section-title mb-20">Best for Your Kids</h2>
                  <p className="bd-section-paragraph">Being brave isn’t always a grand gesture sometimes it just means having a go attempting
                    that difficult question, offering an answer in a lesson when you’re simply really trying new.
                  </p>

                  {/* Promotion counters */}
                  <div className="bd-promotion-counter-wrapper mb-40">
                    {promotionData.map(({ number, text }, index) => (
                      <div className="bd-promotion-counter" key={index}>
                        <div className="bd-promotion-counter-number">
                          <p><span className="counter">{number}</span></p>
                        </div>
                        <div className="bd-promotion-counter-text">
                          {text.map((line, idx) => (
                            <span key={idx}>{line}</span>
                          ))}
                        </div>
                      </div>
                    ))}
                  </div>

                  {/* Feature list */}
                  <div className="bd-about-feature-list">
                    <ul>
                      {features.map((feature, index) => (
                        <li key={index}>
                          <i className="fa-regular fa-check"></i> {feature}
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>

                {/* Buttons */}
                <div className="bd-about-btn d-flex-items gap-30">
                  <Link className="bd-btn btn-primary" href="/about-kindergarten">More Details</Link>
                  <button type='button' onClick={() => playVideo("HKk4oLIzhhM", "youtube")} className="bd-video-btn popup-video">
                    <span className="icon text-xxs-none"><i className="fa-solid fa-play"></i></span>
                    Watch Video
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      {/* -- about area end -- */}
    </>
  );
};

export default AboutKindergarten;
