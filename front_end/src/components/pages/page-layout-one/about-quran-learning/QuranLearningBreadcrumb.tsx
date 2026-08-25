import Image from 'next/image';
import React from 'react';
import QuranBreadcrumb from '../../../../../public/assets/images/breadcrumb/quran-breadcrumb.webp';

const QuranLearningBreadcrumb = () => {
    return (
        <>
            {/* -- quran breadcrumb start -- */}
            <section className="bd-quran-breadcrumb-area theme-bg bd-noise-bg fix">
                <div className="bd-quran-breadcrumb-wrapper">
                    <div className="bd-quran-breadcrumb-content">
                        <div className="bd-quran-breadcrumb-subtile">About Us</div>
                        <h1 className="bd-quran-breadcrumb-title">Master the Quran with Expert Guidance</h1>
                        <p className="bd-quran-breadcrumb-desc">Master the Quran with expert guidance through personalized lessons, deep understanding, and spiritual growth led by experienced, dedicated instructors.</p>
                    </div>
                    <div className="bd-quran-breadcrumb-thumb">
                        <Image src={QuranBreadcrumb} alt="image" />
                    </div>
                </div>
            </section>
            {/* -- quran learning breadcrumb start -- */}
        </>
    );
};

export default QuranLearningBreadcrumb;