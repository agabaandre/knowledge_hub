"use client"
import Image, { StaticImageData } from 'next/image';
import React from 'react';
import { PhotoProvider, PhotoView } from "react-photo-view";
import certificate1 from '../../../../../public/assets/images/certificat/01.webp';
import certificate2 from '../../../../../public/assets/images/certificat/02.webp';
import certificate3 from '../../../../../public/assets/images/certificat/03.webp';
import certificate4 from '../../../../../public/assets/images/certificat/04.webp';
import certificate5 from '../../../../../public/assets/images/certificat/05.webp';
import certificate6 from '../../../../../public/assets/images/certificat/06.webp';

type CertificateProps = {
    imageUrl: StaticImageData;
    altText: string;
};

const certificates: CertificateProps[] = [
    { imageUrl: certificate1, altText: 'certificate 1' },
    { imageUrl: certificate2, altText: 'certificate 2' },
    { imageUrl: certificate3, altText: 'certificate 3' },
    { imageUrl: certificate4, altText: 'certificate 4' },
    { imageUrl: certificate5, altText: 'certificate 5' },
    { imageUrl: certificate6, altText: 'certificate 6' },
];

const StudentCertificateMain: React.FC = () => {
    return (
        <PhotoProvider>
                <div className="col-xl-9 col-lg-9 col-md-8">
                    <div className="bd-dashboard-inner">
                        <div className="bd-dashboard-title-inner">
                            <div className="d-flex justify-content-between">
                                <h4 className="bd-dashboard-title">My Achievement</h4>
                            </div>
                        </div>
                        <div className="bd-dashboard-profile-info">
                            <div className="bd-dashboard-profile-inner">
                                <div className="row g-30">
                                    {certificates.map((certificate, index) => (
                                        <div className="col-lg-4 col-lg-6 col-md-6 col-sm-6" key={index}>
                                            <div className="bd-dashboard-certificate">
                                                <div className="thumb">
                                                    <PhotoView src={certificate.imageUrl.src}>
                                                        <Image style={{ width: "100%", height: "auto" }} src={certificate.imageUrl} alt={certificate.altText} />
                                                    </PhotoView>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </PhotoProvider>
    );
};

export default StudentCertificateMain;
