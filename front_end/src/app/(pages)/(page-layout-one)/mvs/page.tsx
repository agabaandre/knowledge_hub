import MissionVisionMain from '@/components/pages/page-layout-one/mvs/MissionVisionMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Mission Vision Strategy - Education & Online Courses React NextJs Template",
};

const MissionVision = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <MissionVisionMain />
                </main>
            </Wrapper>
        </>
    );
};

export default MissionVision;