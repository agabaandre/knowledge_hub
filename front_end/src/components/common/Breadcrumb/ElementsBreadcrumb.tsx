import Link from 'next/link';
import React from 'react';
interface IElementsBreadcrumb {
    title: string;
    subTitle: string
}
const ElementsBreadcrumb = ({ title, subTitle }: IElementsBreadcrumb) => {
    return (
        <>
            {/* -- elements-breadcrumb end -- */}
            <section className="bd-elements-breadcrumb-area primary-bg">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-xl-12 col-md-12">
                            <div className="bd-elements-breadcrumb-wrapper text-center">
                                <div className="bd-elements-breadcrumb-content">
                                    <h1 className="bd-elements-breadcrumb-title small">{title}</h1>
                                    <p className="bd-elements-description">{subTitle}</p>
                                    <div className="bd-breadcrumb-list-two">
                                        <span><Link href="/"><i className="icon-home"></i>iStudy</Link></span>
                                        <span><Link href="#">Elements</Link></span>
                                        <span><Link href="#">{title}</Link></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- elements-breadcrumb start -- */}
        </>
    );
};

export default ElementsBreadcrumb;