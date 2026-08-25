import AboutModernSchoolingMain from '@/components/pages/page-layout-one/about-modern-schooling/AboutModernSchoolingMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "About Online Course - Education & Online Courses React NextJs Template",
};

const AboutModernSchooling = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <AboutModernSchoolingMain />
                </main>
            </Wrapper>
        </>
    );
};

export default AboutModernSchooling;