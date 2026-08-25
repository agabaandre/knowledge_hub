import Image from "next/image";
import Link from "next/link";
import React from "react";
import LogoWhite from "../../../../../public/assets/images/logo/fav-logo-white.svg";
import CommingSoonBg from "../../../../../public/assets/images/bg/coming-soon.webp";
import CommonCountDownTimer from "@/components/common/CountdownTimer/CommonCountDownTimer";
import { Metadata } from "next";

export const metadata: Metadata = {
    title: "Coming Soon - Education & Online Courses React NextJs Template",
};

const CommingSoon = () => {
    return (
        <>
            {/* -- coming soon area start -- */}
            <section
                className="bd-coming-soon-area bd-coming-soon-bg d-flex align-items-center justify-content-center"
                style={{ backgroundImage: `url(${CommingSoonBg.src})` }}
            >
                <div className="container">
                    <div className="row text-center justify-content-center">
                        <div className="bd-coming-soon-content">
                            <div className="bd-coming-soon-logo">
                                <Link href="/">
                                    <Image src={LogoWhite} alt="logo" />
                                </Link>
                                <p>STAY TUNED</p>
                            </div>
                            <h1 className="bd-coming-soon-title">Coming Soon...</h1>
                            <p className="bd-coming-soon-subtitle">
                                The future is loading – a groundbreaking decentralized experience is just around the
                                corner!
                            </p>
                            {/* Countdown Timer */}
                            <CommonCountDownTimer targetDate="2025-08-05T14:00:00+06:00" />
                            {/* Subscription Form */}
                            <div className="bd-coming-soon-form">
                                <form action="#">
                                    <input type="email" placeholder="Email address" />
                                    <button className="bd-btn btn-primary" type="submit">
                                        Subscribe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- coming soon area end -- */}
        </>
    );
};

export default CommingSoon;
