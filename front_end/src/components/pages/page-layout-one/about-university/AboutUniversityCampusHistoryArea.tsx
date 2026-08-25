import Image, { StaticImageData } from 'next/image';
import React from 'react';
import universityCampusImg from '../../../../../public/assets/images/history/university-cumpas.webp';
import historyImgOne from '../../../../../public/assets/images/history/history-img-1.webp';
import historyImgTwo from '../../../../../public/assets/images/history/history-img-2.webp';
import historyImgThree from '../../../../../public/assets/images/history/history-img-3.webp';
import Link from 'next/link';

interface HistoryItem {
    id: number;
    image: StaticImageData;
    title: string;
    description: string;
}

const historyData: HistoryItem[] = [
    {
        id: 1,
        image: historyImgOne,
        title: 'Indigenous Education at iStudy University',
        description: 'iStudy University honors its commitment to Indigenous education, fostering a diverse and inclusive environment while supporting the academic success of Native students.',
    },
    {
        id: 2,
        image: historyImgTwo,
        title: `Women's Achievements at iStudy University`,
        description: `Celebrates Women's achievements, empowering female students and alumni through innovative programs, leadership opportunities, and a commitment to gender equality.`,
    },
    {
        id: 3,
        image: historyImgThree,
        title: 'International Students at iStudy University',
        description: 'Welcomes international students, fostering a global community with diverse perspectives, offering support services, and enriching the academic and cultural experience.'
    }
];

const AboutUniversityCampusHistoryArea = () => {
    return (
        <>
            <section className="bd-campus-history-area section-space primary-bg">
                <div className="container">
                    <div className="row gy-30 align-items-center justify-content-between section-title-space">
                        <div className="col-xl-6 col-lg-6">
                            <div className="bd-section-wrapper">
                                <h2 className="bd-section-title">The history of iStudy</h2>
                            </div>
                        </div>
                        <div className="col-xl-4 col-lg-6">
                            <div className="bd-history-btn text-lg-end text-start">
                                <Link className="bd-modern-btn" href="/history">
                                    <span className="text">Explore all of iStudy’s history</span>
                                    <i className="fa-regular fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30 align-items-center justify-content-between">
                        <div className="col-xl-6 col-lg-12">
                            <div className="bd-history-thumb">
                                <Image src={universityCampusImg} style={{ height: 'auto', width: 'auto' }} alt="image" />
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-12">
                            <div className="bd-history-of-university">
                                <p>
                                    iStudy University was founded on October 28, 1636, as one of the first higher
                                    education institutions in the region. Established by a decree of the Great and General
                                    Court, it quickly became a center for academic excellence. The university’s initial
                                    endowment came from a donation of 400 books and half an estate, setting a strong
                                    foundation for its future.
                                </p>
                                <p>
                                    In 1721, a notable benefactor contributed funds specifically for a {`"Divinity Professor,"`}{" "}
                                    introducing the practice of earmarked donations. This marked a turning point in the{" "}
                                    {`university's`} growth. Today, iStudy University honors its rich heritage while
                                    continuing to lead in education and research on a global scale.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="section-space-top">
                        <div className="row gy-30">
                            {historyData.map((item) => (
                                <div className="col-xl-4 col-lg-4 col-md-6" key={item.id}>
                                    <div className="bd-history-box">
                                        <div className="thumb">
                                            <Image src={item.image} style={{ height: 'auto', width: 'auto' }} alt="image" />
                                        </div>
                                        <div className="bd-history-box-content">
                                            <h4 className="title underline mb-20">
                                                <Link href='/history'>{item.title}</Link>
                                            </h4>
                                            <p className="description">{item.description}</p>
                                            <Link className="bd-btn btn-outline-primary" href='/history'>Learn More</Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default AboutUniversityCampusHistoryArea;