import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import ctaBook1 from '../../../public/assets/images/cta/cta-book-1.webp';
import ctaBook2 from '../../../public/assets/images/cta/cta-book-2.webp';
import { ICtaData } from '@/interFace/interFace';

const BookCtaArea = () => {
    const ctaData:ICtaData[] = [
        {
            id: 1,
            subtitle: 'New',
            title: 'Discover the Latest Richard Osman Novel',
            buttonText: 'Shop Now',
            buttonLink: '/shop',
            image: ctaBook1,
            bgClass: 'bg-1',
        },
        {
            id: 2,
            subtitle: 'Gift',
            title: 'The Perfect Book Gift for a Loved One',
            buttonText: 'Shop Now',
            buttonLink: '/shop-v2',
            image: ctaBook2,
            bgClass: 'bg-2',
        },
    ];

    return (
        <>
            {/* -- book cta area start -- */}
            <div className="bd-book-cta-area section-space-bottom">
                <div className="container">
                    <div className="row gy-30">
                        {ctaData.map((item) => (
                            <div className="col-xl-6 col-lg-6 col-md-12" key={item.id}>
                                <div className="bd-cta-wrapper style-one">
                                    <div className={`bd-cta-item ${item.bgClass}`}>
                                        <div className="bd-cta-content">
                                            <span className="bd-cta-subtitle">{item.subtitle}</span>
                                            <h4 className="bd-cta-tittle">
                                                <Link href={item.buttonLink}>{item.title}</Link>
                                            </h4>
                                            <div className="bd-cta-btn">
                                                <Link
                                                    className={`bd-btn ${item.bgClass === 'bg-1' ? 'btn-secondary' : 'btn-primary'} btn-small`}
                                                    href={item.buttonLink}
                                                >
                                                    {item.buttonText}
                                                </Link>
                                            </div>
                                        </div>
                                        <div className="bd-cta-bg">
                                            <Image src={item.image} style={{width:"100%", height:"auto"}} alt={item.title} priority/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            {/* -- book cta area end -- */}
        </>
    );
};

export default BookCtaArea;
