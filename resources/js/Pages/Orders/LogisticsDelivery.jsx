import { Head, useForm } from "@inertiajs/react";
import ContentPanel from "../../Components/Table/ContentPanel";
import { Check, X } from "lucide-react";
import LoginInputTooltip from "../../Components/Tooltip/LoginInputTooltip";
import { useState } from "react";

const LogisticsDelivery = ({ page_title, order, lines }) => {
    const [uploadedFile, setUploadedFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(null);
    const { data, setData, post, processing, errors, reset } = useForm({
        order_id: order.id,
        proof_of_delivery: "",
    });

    const handleFileUpload = (e) => {
        const file = e.target.files?.[0];
        if (file) {
            setUploadedFile(file);
            setPreviewUrl(URL.createObjectURL(file)); // generate preview URL
        }
        setData("proof_of_delivery", file);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        Swal.fire({
            title: `<p class="font-nunito-sans" >Are you sure that you want to proceed?</p>`,
            showCancelButton: true,
            confirmButtonText: "Confirm",
            confirmButtonColor: "#201E43",
            cancelButtonColor: "#134B70",
            icon: "question",
            iconColor: "#134B70",
            reverseButtons: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    post("/orders/update_save", {
                        onSuccess: (response) => {
                            console.log(response);
                            handleToast(
                                "Order Updated successfully",
                                "success"
                            );
                        },
                    });
                } catch (error) {
                    {
                        console.log(error);
                    }
                }
            }
        });
    };

    return (
        <>
            <Head title={page_title} />
            <form onSubmit={handleSubmit}>
                <ContentPanel>
                    <div className="flex justify-center items-center">
                        <div className="bg-white md:border border-gray-400 rounded-lg shadow-sm md:w-2/3">
                            <div className="p-6 border-b border-gray-200">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h2 className="text-2xl font-semibold text-gray-900">
                                            Customer Order Details
                                        </h2>
                                        <p className="text-sm text-gray-600 mt-1">
                                            Order #{order.reference_number} •
                                            Submitted on{" "}
                                            {new Date(
                                                order.created_at
                                            ).toLocaleDateString()}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div className="p-6">
                                <div className="space-y-6">
                                    {/* Customer Information Section */}
                                    <div className="bg-gray-50 p-4 rounded-lg">
                                        <h3 className="text-lg font-medium text-gray-900 mb-4">
                                            Customer Information
                                        </h3>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {/* Customer Name */}
                                            <div className="space-y-1">
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Customer Name
                                                </label>
                                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                                    {order.first_name +
                                                        " " +
                                                        order.last_name}
                                                </div>
                                            </div>

                                            {/* Email Address */}
                                            <div className="space-y-1">
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Email Address
                                                </label>
                                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                                    {order.email_address}
                                                </div>
                                            </div>

                                            {/* Contact Details */}
                                            <div className="space-y-1">
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Contact Details
                                                </label>
                                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                                    {order.contact_details}
                                                </div>
                                            </div>

                                            {/* Delivery Address */}
                                            <div className="space-y-1">
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Delivery Address
                                                </label>
                                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900 whitespace-pre-line">
                                                    {order.delivery_address}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Financial Information Section */}
                                    <div className="bg-blue-50 p-4 rounded-lg">
                                        <h3 className="text-lg font-medium text-gray-900 mb-4">
                                            Downpayment Details
                                        </h3>
                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            {/* Downpayment Status */}
                                            <div className="space-y-1">
                                                <label className="block text-sm font-medium mb-2 text-gray-700">
                                                    Downpayment
                                                </label>
                                                <span
                                                    className={`inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${
                                                        order.has_downpayment ==
                                                        "yes"
                                                            ? "bg-green-400 text-gray-700"
                                                            : "bg-red-100 text-red-800"
                                                    }`}
                                                >
                                                    {order.has_downpayment ==
                                                    "yes" ? (
                                                        <Check size={12} />
                                                    ) : (
                                                        <X size={12} />
                                                    )}
                                                    {order.has_downpayment ==
                                                    "yes"
                                                        ? "Yes"
                                                        : "No"}
                                                </span>
                                            </div>

                                            {/* Downpayment Value */}
                                            {order.has_downpayment == "yes" && (
                                                <div className="space-y-1">
                                                    <label className="block text-sm font-medium text-gray-700">
                                                        Downpayment Value
                                                    </label>
                                                    <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900 font-medium">
                                                        {
                                                            order.downpayment_value
                                                        }
                                                    </div>
                                                </div>
                                            )}

                                            {/* Financed Amount */}
                                            <div className="space-y-1">
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Financed Amount
                                                </label>
                                                <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900 font-medium">
                                                    {order.financed_amount}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Item Information Section */}
                                    <div className="bg-purple-50 p-4 rounded-lg mb-4">
                                        <h3 className="text-lg font-medium text-gray-900 mb-4">
                                            Item Details
                                        </h3>
                                        <div className="overflow-x-auto bg-white border border-gray-200 rounded-lg">
                                            <table className="min-w-full divide-y divide-gray-200 text-sm text-left">
                                                <thead className="bg-gray-100">
                                                    <tr>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            #
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Description
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Model
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Color
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Storage
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Qty
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-gray-100">
                                                    {lines.map(
                                                        (item, index) => (
                                                            <tr key={index}>
                                                                <td className="px-4 py-2 text-gray-700">
                                                                    {index + 1}
                                                                </td>
                                                                <td className="px-4 py-2 text-gray-900">
                                                                    {
                                                                        item.item_description
                                                                    }
                                                                </td>
                                                                <td className="px-4 py-2 text-gray-900">
                                                                    {item.model}
                                                                </td>
                                                                <td className="px-4 py-2 text-gray-900">
                                                                    {
                                                                        item.actual_color
                                                                    }
                                                                </td>
                                                                <td className="px-4 py-2 text-gray-900">
                                                                    {item.size}
                                                                </td>
                                                                <td className="px-4 py-2 text-gray-900">
                                                                    {item.qty}
                                                                </td>
                                                            </tr>
                                                        )
                                                    )}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {/* Contract Information Section */}
                                    {order.approved_contract && (
                                        <div className="bg-green-50 p-4 rounded-lg">
                                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                                Contract Information
                                            </h3>
                                            <div className="bg-white border border-gray-200 rounded-lg p-4">
                                                <div className="mb-4">
                                                    {order.approved_contract.endsWith(
                                                        ".pdf"
                                                    ) ? (
                                                        <iframe
                                                            src={`/contract/uploaded-contract/${order.approved_contract}`}
                                                            className="w-full h-96 border rounded"
                                                            title="PDF Viewer"
                                                        ></iframe>
                                                    ) : (
                                                        <img
                                                            src={`/contract/uploaded-contract/${order.approved_contract}`}
                                                            alt="Proof of Payment"
                                                            className="max-w-full h-auto rounded"
                                                        />
                                                    )}
                                                </div>
                                                <div className="flex items-center gap-3">
                                                    <svg
                                                        className="h-8 w-8 text-green-600"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth={2}
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                        />
                                                    </svg>
                                                    <div className="flex-1">
                                                        <div className="font-medium text-gray-900">
                                                            {order.approved_contract.substring(
                                                                order.approved_contract.lastIndexOf(
                                                                    "_"
                                                                ) + 1
                                                            )}
                                                        </div>
                                                        <div className="text-sm text-gray-500">
                                                            Approved contract
                                                            document
                                                        </div>
                                                    </div>
                                                    <a
                                                        href={`/contract/uploaded-contract/${order.approved_contract}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors"
                                                    >
                                                        View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                    {order.schedule_date && (
                                        <div className="bg-gray-300 p-4 rounded-lg">
                                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                                Schedule Information
                                            </h3>
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                {/* Customer Name */}
                                                <div className="space-y-1">
                                                    <label className="block text-sm font-medium text-gray-700">
                                                        Schedule Date
                                                    </label>
                                                    <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                                        {
                                                            new Date(
                                                                order.schedule_date
                                                            )
                                                                .toISOString()
                                                                .split("T")[0]
                                                        }
                                                    </div>
                                                </div>

                                                {/* Email Address */}
                                                <div className="space-y-1">
                                                    <label className="block text-sm font-medium text-gray-700">
                                                        Transaction Type
                                                    </label>
                                                    <div className="px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-900">
                                                        {order.transaction_type}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Upload Downpayment Receipt */}
                                    <div className="space-y-2">
                                        <label
                                            htmlFor="proof_of_delivery"
                                            className="block text-sm font-medium text-gray-700"
                                        >
                                            Upload Proof of Delivery
                                        </label>
                                        <div
                                            className={`relative border-2 ${
                                                errors.proof_of_delivery
                                                    ? "border-red-500"
                                                    : "border-dashed border-gray-400 hover:border-gray-400"
                                            }  rounded-lg p-6 text-center  transition-colors cursor-pointer`}
                                        >
                                            <input
                                                id="proof_of_delivery"
                                                name="proof_of_delivery"
                                                type="file"
                                                accept=".jpg,.jpeg,.png"
                                                onChange={handleFileUpload}
                                                className="hidden"
                                            />
                                            <label
                                                htmlFor="proof_of_delivery"
                                                className="cursor-pointer flex flex-col items-center gap-2"
                                            >
                                                {uploadedFile ? (
                                                    <>
                                                        {" "}
                                                        {previewUrl && (
                                                            <img
                                                                src={previewUrl}
                                                                alt="Preview"
                                                                className="mt-4 max-h-48 mx-auto rounded border"
                                                            />
                                                        )}
                                                        <svg
                                                            className="h-8 w-8 text-green-600"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth={2}
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                            />
                                                        </svg>
                                                        <span className="text-sm font-medium text-green-600">
                                                            {uploadedFile.name}
                                                        </span>
                                                        <span className="text-xs text-gray-500">
                                                            Click to change file
                                                        </span>
                                                    </>
                                                ) : (
                                                    <>
                                                        <svg
                                                            className="h-8 w-8 text-gray-400"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                strokeLinecap="round"
                                                                strokeLinejoin="round"
                                                                strokeWidth={2}
                                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                                            />
                                                        </svg>
                                                        <span className="text-sm font-medium text-gray-700">
                                                            Click to Upload
                                                            Proof of Delivery
                                                        </span>
                                                        <span className="text-xs text-gray-500">
                                                            JPG, PNG up to 10MB
                                                        </span>
                                                    </>
                                                )}
                                            </label>
                                            {errors.proof_of_delivery && (
                                                <LoginInputTooltip
                                                    content={
                                                        errors.proof_of_delivery
                                                    }
                                                >
                                                    <i className="fa-solid fa-circle-info text-red-600 absolute cursor-pointer top-1/2 text-xs md:text-base right-1.5 md:right-3 transform -translate-y-1/2"></i>
                                                </LoginInputTooltip>
                                            )}
                                        </div>
                                    </div>

                                    {/* Action Buttons */}
                                    <div className="flex justify-between gap-4 pt-4 border-t border-gray-200">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                (window.location.href =
                                                    "/orders")
                                            }
                                            className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                        >
                                            Back
                                        </button>

                                        <button
                                            type="submit"
                                            className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-green-500 hover:brightness-90"
                                        >
                                            Deliver
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </ContentPanel>
            </form>
        </>
    );
};

export default LogisticsDelivery;
