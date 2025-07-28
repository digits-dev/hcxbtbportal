
import { Head, Link, router, useForm } from "@inertiajs/react";
import AnimationData from '../../../../public/Animations/no-data-found-anim.json'
import Lottie from "lottie-react";
import { Clock } from "lucide-react";


const RegisterLinkExpired = ({ email }) => {
    return (
        <>
            <Head title='Link Expired'/>
            <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-black p-4">
                {/* CARD */}
                <div className="h-full flex flex-col-reverse md:flex-row items-center max-w-4xl p-20 backdrop-blur-sm bg-white border border-gray-200/50 shadow-2xl shadow-black/20 rounded-lg overflow-hidden">
                    <div className="items-center flex  flex-col md:items-start">
                        <p className="text-xl font-bold">Link is not valid any more</p>
                        <p className="mb-5 text-sm">Oops! This URL is not valid anymore</p>

                         <div className="bg-slate-50/80 rounded-xl p-6 border border-slate-200/50 mb-5">
                            <div className="flex items-start gap-4">
                                <div className="w-10 h-10 bg-slate-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <Clock className="h-5 w-5 text-slate-600" />
                                </div>
                                <div className="space-y-1">
                                    <h3 className="font-semibold text-slate-900">Why did this happen?</h3>
                                    <p className="text-slate-600 text-sm">
                                        Security links automatically expire after a set time period to protect your account and ensure your data stays safe.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <Link href='/login' className={`font-open-sans bg-gradient-to-br from-gray-900 via-gray-800 to-black mt-4 text-white p-2 text-[10px] cursor-pointer rounded-lg text-center font-medium hover:opacity-70 md:text-base md:p-2.5 disabled:cursor-not-allowed `}>Back to Login</Link>
                    </div>
                    <Lottie animationData={AnimationData} className='w-full h-full max-w-48 max-h-48 md:max-w-[26rem] md:max-h-[26rem]' style={{background: 'transparent'}}/>
                </div>
            </div>
        </>
    );
};

export default RegisterLinkExpired;
