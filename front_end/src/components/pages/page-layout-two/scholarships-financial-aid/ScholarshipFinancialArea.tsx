import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import { IScholarshipFinancialAid } from '@/interFace/interFace';
import scholarshipImgOne from '../../../../../public/assets/images/financial/scholarships-financial-1.webp';
import scholarshipImgTwo from '../../../../../public/assets/images/financial/scholarships-financial-2.webp';
import historyImgOne from '../../../../../public/assets/images/history/history-img-1.webp';
import historyImgTwo from '../../../../../public/assets/images/history/history-img-3.webp';

 const scholarshipFinancialAidData: IScholarshipFinancialAid[] = [
    {
        image: scholarshipImgOne,
        title: 'Undergraduate Students',
        link: '/scholarships-financial-aid-details'
    },
    {
        image: scholarshipImgTwo,
        title: 'Graduate Students',
        link: '/scholarships-financial-aid-details'
    },
    {
        image: historyImgOne,
        title: 'Indigenous Commonwealth Scholarships',
        link: '/scholarships-financial-aid-details'
    },
    {
        image: historyImgTwo,
        title: 'International Student Scholarship',
        link: '/scholarships-financial-aid-details'
    }
];

const ScholarshipFinancialArea = () => {
    return (
        <>
            <section className="bd-scholarships-financial-aid-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-8">
                            <div className="bd-section-title-wrapper section-title-space text-center">
                                <h2 className="bd-section-title">Financial Aid Policy - Summer 2024</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30 justify-content-center">
                        {
                            scholarshipFinancialAidData.map((item, index) => <div key={index} className="col-xl-6 col-lg-6 col-md-12">
                            <div className="bd-scholarships-financial-aid-box">
                                <div className="thumb">
                                    <Link href="/scholarships-financial-aid-details">
                                        <Image src={item.image} style={{width: '100%', height: 'auto'}} alt="image"/>
                                    </Link>
                                </div>
                                <h4 className="title underline">
                                    <Link href="/scholarships-financial-aid-details">{item.title}</Link></h4>
                            </div>
                        </div>)
                        }
                    </div>
                </div>
            </section>
        </>
    );
};

export default ScholarshipFinancialArea;