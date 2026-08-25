import React from 'react';
import MainFooter from './MainFooter';
import Link from 'next/link';
import { CommonFooterMenuLinks } from '@/data/footer-menu/footer-menu-data';

const CommonFooter = () => {
    return (
        <>
            <MainFooter>
                {CommonFooterMenuLinks.map((footerLink, index) => (
                    <div className={`col-xxl-3 col-xl-3 col-lg-3 col-md-3 col-sm-6`} key={index}>
                        <div className={`bd-footer-widget ${footerLink.spacingClass}`}>
                            <h6 className="bd-footer-widget-title">{footerLink.title}</h6>
                            <div className="bd-footer-widget-links list-none">
                                <ul>
                                    {footerLink.links.map((link, idx) => (
                                        <li className="underline" key={idx}>
                                            <Link href={link.href}>{link.name}</Link>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    </div>
                ))}
            </MainFooter>
        </>
    );
};

export default CommonFooter;