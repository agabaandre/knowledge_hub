import Link from 'next/link';
import React from 'react';
import cerficate1 from "../../../../public/assets/images/certificat/01.webp";
import cerficate2 from "../../../../public/assets/images/certificat/02.webp";
import cerficate3 from "../../../../public/assets/images/certificat/03.webp";
import cerficate4 from "../../../../public/assets/images/certificat/04.webp";
import cerficate5 from "../../../../public/assets/images/certificat/05.webp";
import cerficate6 from "../../../../public/assets/images/certificat/06.webp";
import Image from 'next/image';

const CertificateTemplates = () => {
    return (
        <>
            <div className="row g-30">
                <div className="col-lg-4 col-md-6 col-sm-6">
                    <div className="bd-dashboard-certificate">
                        <div className="thumb">
                            <Link href="#"><Image style={{width:"100%", height:"auto"}} src={cerficate1} alt="certificate not found" /></Link>
                        </div>
                    </div>
                </div>
                <div className="col-lg-4 col-md-6 col-sm-6">
                    <div className="bd-dashboard-certificate">
                        <div className="thumb">
                            <Link href="#"><Image style={{width:"100%", height:"auto"}} src={cerficate2} alt="certificate not found" /></Link>
                        </div>
                    </div>
                </div>
                <div className="col-lg-4 col-md-6 col-sm-6">
                    <div className="bd-dashboard-certificate">
                        <div className="thumb">
                            <Link href="#"><Image style={{width:"100%", height:"auto"}} src={cerficate3} alt="certificate not found" /></Link>
                        </div>
                    </div>
                </div>
                <div className="col-lg-4 col-md-6 col-sm-6">
                    <div className="bd-dashboard-certificate">
                        <div className="thumb">
                            <Link href="#"><Image style={{width:"100%", height:"auto"}} src={cerficate4} alt="certificate not found" /></Link>
                        </div>
                    </div>
                </div>
                <div className="col-lg-4 col-md-6 col-sm-6">
                    <div className="bd-dashboard-certificate">
                        <div className="thumb">
                            <Link href="#"><Image style={{width:"100%", height:"auto"}} src={cerficate5} alt="certificate not found" /></Link>
                        </div>
                    </div>
                </div>
                <div className="col-lg-4 col-md-6 col-sm-6">
                    <div className="bd-dashboard-certificate">
                        <div className="thumb">
                            <Link href="#"><Image style={{width:"100%", height:"auto"}} src={cerficate6} alt="certificate not found" /></Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default CertificateTemplates;