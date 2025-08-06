import { Head, useForm } from "@inertiajs/react";
import ContentPanel from "../../Components/Table/ContentPanel";
import { Check, Forklift, HandHelping, X } from "lucide-react";
import { useState } from "react";
import InputComponent from "../../Components/Forms/Input";
import { useTheme } from "../../Context/ThemeContext";
import useThemeStyles from "../../Hooks/useThemeStyles";
import { useToast } from "../../Context/ToastContext";
import TextArea from "../../Components/Forms/TextArea";

const LogisticsSchedule = ({ page_title, order, lines }) => {
    const { handleToast } = useToast();
    const { theme } = useTheme();
    const { primayActiveColor, textColorActive, buttonSwalColor } =
        useThemeStyles(theme);
    const { data, setData, post, processing, errors, reset } = useForm({
        order_id: order.id,
        schedule_date: "",
        transaction_type: "logistics",
        carrier_name: "",
        delivery_reference: "",
        logistics_remarks: "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        Swal.fire({
            title: `<p class="font-poppins text-3xl" >Do you want to schedule this Order?</p>`,
            showCancelButton: true,
            confirmButtonText: `Schedule`,
            confirmButtonColor: buttonSwalColor,
            icon: "question",
            iconColor: buttonSwalColor,
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                post("/orders/update_save", {
                    onSuccess: (data) => {
                        const { message, type } = data.props.auth.sessions;
                        handleToast(message, type);
                    },
                    onError: (data) => {
                        const { message, type } = data.props.auth.sessions;
                        handleToast(message, type);
                    },
                });
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
                                            {order.status_name}
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
                                                            Color
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Storage
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Qty
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            Serial
                                                        </th>
                                                        <th className="px-4 py-2 font-medium text-gray-600">
                                                            IMEI
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
                                                                <td className="px-4 py-2 text-gray-900">
                                                                    {
                                                                        item.serial_no
                                                                    }
                                                                </td>
                                                                <td className="px-4 py-2 text-gray-900">
                                                                    {item.imei}
                                                                </td>
                                                            </tr>
                                                        )
                                                    )}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {/* Schedule Date */}
                                    <InputComponent
                                        min={
                                            new Date()
                                                .toISOString()
                                                .split("T")[0]
                                        }
                                        type="date"
                                        name="schedule_date"
                                        onChange={(e) =>
                                            setData(
                                                "schedule_date",
                                                e.target.value
                                            )
                                        }
                                        onError={errors.schedule_date}
                                    />

                                    {/* Delivery Option */}
                                    <div className="mt-2">
                                        <label
                                            className={`block text-xs font-bold ${
                                                theme === "bg-skin-black"
                                                    ? " text-gray-400"
                                                    : "text-gray-700"
                                            }  font-poppins`}
                                        >
                                            Delivery Option
                                        </label>
                                        <div className="relative rounded-lg mt-1 flex space-x-1 overflow-hidden border-2 bg-gray-300">
                                            <div
                                                className={`absolute ${theme} rounded-md h-full w-1/2 transition-all duration-300 ${
                                                    data.transaction_type ===
                                                    "logistics"
                                                        ? "left-0"
                                                        : "left-1/2"
                                                }`}
                                            ></div>
                                            <button
                                                type="button"
                                                className={` flex-1 flex items-center justify-center py-1 z-10 outline-none text-sm font-medium
                                                ${
                                                    data.transaction_type ===
                                                    "logistics"
                                                        ? "text-white"
                                                        : "text-black/50"
                                                }`}
                                                onClick={() =>
                                                    setData({
                                                        ...data,
                                                        transaction_type:
                                                            "logistics",
                                                        carrier_name: "",
                                                        logistics_remarks: "",
                                                        delivery_reference: "",
                                                    })
                                                }
                                            >
                                                <Forklift className="w-5 h-5 mr-2" />{" "}
                                                Logistics
                                            </button>
                                            <button
                                                type="button"
                                                className={`flex-1 flex items-center justify-center py-1.5 z-10 outline-none text-sm font-medium
                                                ${
                                                    data.transaction_type ==
                                                    "third party"
                                                        ? "text-white"
                                                        : "text-black/50"
                                                }`}
                                                onClick={() =>
                                                    setData({
                                                        ...data,
                                                        transaction_type:
                                                            "third party",
                                                        carrier_name: "",
                                                        logistics_remarks: "",
                                                        delivery_reference: "",
                                                    })
                                                }
                                            >
                                                <HandHelping className="w-5 h-5 mr-2" />{" "}
                                                Third Party
                                            </button>
                                        </div>
                                    </div>

                                    {data.transaction_type == "logistics" && (
                                        <TextArea
                                            placeholder="Enter Remarks"
                                            rows={4}
                                            name="logistics_remarks"
                                            displayName={"Remarks"}
                                            onChange={(e) =>
                                                setData(
                                                    "logistics_remarks",
                                                    e.target.value
                                                )
                                            }
                                            onError={errors.logistics_remarks}
                                        />
                                    )}

                                    {data.transaction_type == "third party" && (
                                        <>
                                            <InputComponent
                                                placeholder="Enter Carrier Name"
                                                name="carrier_name"
                                                onChange={(e) =>
                                                    setData(
                                                        "carrier_name",
                                                        e.target.value
                                                    )
                                                }
                                                onError={errors.carrier_name}
                                            />
                                            <InputComponent
                                                placeholder="Enter Delivery Ref#"
                                                name="delivery_reference"
                                                onChange={(e) =>
                                                    setData(
                                                        "delivery_reference",
                                                        e.target.value
                                                    )
                                                }
                                                onError={
                                                    errors.delivery_reference
                                                }
                                            />
                                        </>
                                    )}

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
                                            className="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white bg-black hover:brightness-90"
                                        >
                                            Schedule
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

export default LogisticsSchedule;
