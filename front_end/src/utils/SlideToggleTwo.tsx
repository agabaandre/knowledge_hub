import useGlobalContext from "@/hooks/useContexts";
import { motion, AnimatePresence } from "framer-motion";

interface SlideToggleProps {
    children: React.ReactNode;
    className?: string;
}

const SlideToggleTwo: React.FC<SlideToggleProps> = ({ children, className = "" }) => {
    const { isOpen } = useGlobalContext();

    return (
        <AnimatePresence>
            {isOpen && (
                <motion.div
                    initial={{ height: 0, opacity: 0 }}
                    animate={{ height: "auto", opacity: 1 }}
                    exit={{ height: 0, opacity: 0 }}
                    transition={{ duration: 0.6, ease: "easeInOut" }}
                    className={`${className}`}
                >
                    {children}
                </motion.div>
            )}
        </AnimatePresence>
    );
};

export default SlideToggleTwo;