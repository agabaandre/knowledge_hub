import Link from 'next/link';
import React from 'react';

const HistoryArea = () => {
    return (
        <>
            <section className="bd-promotion-area section-space">
                <div className="container">
                    <div className="row g-30 justify-content-between align-items-center">
                        <div className="col-xl-6 col-lg-6 col-md-6">
                            <div className="bd-section-title-wrapper">
                                <h2 className="bd-section-title mb-20">Explore Centuries of {`iStudy's`} Legacy</h2>
                                <p className="bd-section-paragraph">iStudy University is renowned for its long-standing
                                    tradition of academic excellence and innovation. Yet, even devoted alumni may be
                                    surprised by these milestones and historical highlights.</p>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6 col-md-6">
                            <div className="bd-history-centuries-list list-none">
                                <ul>
                                    <li className="underline"><Link href="#1600s">1600s: The founding of iStudy</Link></li>
                                    <li className="underline"><Link href="#1700s">1700s: {`iStudy's`} role in early education</Link>
                                    </li>
                                    <li className="underline"><Link href="#1800s">1800s: A period of growth and expansion</Link></li>
                                    <li className="underline"><Link href="#2000s">2000s: A new era of innovation and discovery</Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default HistoryArea;