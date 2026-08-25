import LanguageAcademyMain from '@/components/language-academy/LanguageAcademyMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Language Academy - Education & Online Courses React NextJs Template",
};

const LanguageAcademy = () => {
    return (
        <>
            <Wrapper>
                <main className="main-area">
                    <LanguageAcademyMain />
                </main>
            </Wrapper>
        </>
    );
};

export default LanguageAcademy;