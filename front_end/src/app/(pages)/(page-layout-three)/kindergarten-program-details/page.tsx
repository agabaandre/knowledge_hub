import KindergartenProgramDetailsMain from '@/components/pages/page-layout-three/kindergartent-program-details/KindergartenProgramDetailsMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "KG Program Details - Education & Online Courses React NextJs Template",
};

const KindergartenProgramDetails = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <KindergartenProgramDetailsMain id={7} />
                </main>
            </Wrapper>
        </>
    );
};

export default KindergartenProgramDetails;