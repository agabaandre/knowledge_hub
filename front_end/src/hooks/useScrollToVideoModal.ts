import { useRef } from "react";

// Define the type for the callback function
type OpenVideoModalCallback = () => void;

const useScrollToVideoModal = (openVideoModalCallback: OpenVideoModalCallback) => {
    const sectionRef = useRef<HTMLDivElement | null>(null);

    const handleOpenVideoModal = () => {
        if (sectionRef.current) {
            sectionRef.current.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        openVideoModalCallback(); 
    };

    return { sectionRef, handleOpenVideoModal };
};

export default useScrollToVideoModal;
