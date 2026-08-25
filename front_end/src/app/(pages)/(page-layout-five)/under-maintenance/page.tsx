import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import Logo from '../../../../../public/assets/images/logo/fav-logo-white.svg';
import maintenanceBg from '../../../../../public/assets/images/bg/maintenance-bg.webp';
import CommonCountDownTimer from '@/components/common/CountdownTimer/CommonCountDownTimer';
import { Metadata } from 'next';

export const metadata: Metadata = {
    title: "Under Maintence - Education & Online Courses React NextJs Template",
};

const UnderMaintence = () => {
    return (
        <>
            {/* -- under-maintenance area start -- */}
            <section className="bd-under-maintenance bd-coming-soon-bg d-flex align-items-center justify-content-center" style={{ backgroundImage: `url(${maintenanceBg.src})` }}>
                <div className="container">
                    <div className="row text-center justify-content-center">
                        <div className="bd-coming-soon-content">
                            <div className="bd-coming-soon-logo">
                                <Link href="/"><Image src={Logo} alt="logo" /></Link>
                                <p>STAY TUNED</p>
                            </div>
                            <h1 className="bd-coming-soon-title">Under Maintenance</h1>
                            <p className="bd-coming-soon-subtitle">We’re upgrading the chain – your seamless decentralized experience will return soon!</p>
                            <CommonCountDownTimer targetDate="2025-08-05T14:00:00+06:00" />
                            <div className="bd-coming-soon-form">
                                <form action="#">
                                    <input type="email" placeholder="Email address" />
                                    <button className="bd-btn btn-primary" type="submit">Subscribe</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- under-maintenance area end -- */}
        </>
    );
};

export default UnderMaintence;