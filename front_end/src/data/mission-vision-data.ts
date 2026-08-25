import missionIcon from '../../public/assets/images/icon/mission.svg';
import visionIcon from '../../public/assets/images/icon/vision.svg';
import valuesIcon from '../../public/assets/images/icon/values.svg';
import { IMissionVision } from '@/interFace/interFace';

export const missionVisionData:IMissionVision[] = [
    {
        id: 1,
        img: missionIcon,
        title: 'Our Mission',
        description: "Our mission is to provide high-quality, accessible online education that empowers learners to reach their personal and professional goals. We aim to create an innovative, flexible, and engaging learning environment for all."
    },
    {
        id: 2,
        img: visionIcon,
        title: 'Our Vision',
        description: "We envision a future where education is globally accessible, enabling individuals to unlock their full potential.Through continuous innovation, we strive to be a leader in transforming lives through online learning."
    },
    {
        id: 3,
        img: valuesIcon,
        title: 'Our Values',
        description: "At IStudy, we are guided by integrity a commitment to excellence.We believe in putting learners first ensuring education is accessible to all.Our goal is to deliver exceptional learning experiences that drive success."
    },
]