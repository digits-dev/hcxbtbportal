import { Head, useForm } from "@inertiajs/react";
import ContentPanel from "../../Components/Table/ContentPanel";
import { useState, useMemo, useEffect } from "react";
import { useToast } from "../../Context/ToastContext";
import CustomSelect from "../../Components/Dropdown/CustomSelect";
import axios from "axios";
import InputComponent from "../../Components/Forms/Input";
import TextArea from "../../Components/Forms/TextArea";
import { useTheme } from "../../Context/ThemeContext";
import LoginInputTooltip from "../../Components/Tooltip/LoginInputTooltip";
import useThemeStyles from "../../Hooks/useThemeStyles";

const CreateOrderForm = ({ page_title, sku }) => {
    const { handleToast } = useToast();
    const { theme } = useTheme();
    const { primayActiveColor, textColorActive, buttonSwalColor } = useThemeStyles(theme);
    const [uploadedFile, setUploadedFile] = useState(null);
    const [modelFilter, setModelFilter] = useState("");
    const [colorFilter, setColorFilter] = useState("");
    const [sizeFilter, setSizeFilter] = useState("");
    const [selectedItemForSelect, setSelectedItemForSelect] = useState(null);
    const [selectedProducts, setSelectedProducts] = useState([]);
    const { data, setData, post, processing, errors, reset } = useForm({
        first_name: "",
        last_name: "",
        delivery_address: "",
        email_address: "",
        contact_details: "+639",
        financed_amount: "",
        has_downpayment: "no",
        downpayment_value: "",
        approved_contract: "",
        items: [],
    });
    

    const skuOptions = sku.map((item) => ({
        value: item.digits_code,
        label: item.item_description,
        model: item.model,
        color: item.actual_color,
        size: item.size,
    }));

    // Get unique values for filter dropdowns
    const uniqueModels = useMemo(() => {
        const models = [...new Set(skuOptions.map((sku) => sku.model))].sort();
        return [
            { value: "", label: "All Models" },
            ...models.map((model) => ({ value: model, label: model })),
        ];
    }, []);

    const uniqueColors = useMemo(() => {
        const colors = [...new Set(skuOptions.map((sku) => sku.color))].sort();
        return [
            { value: "", label: "All Colors" },
            ...colors.map((color) => ({ value: color, label: color })),
        ];
    }, []);

    const uniqueSizes = useMemo(() => {
        const sizes = [
            ...new Set(
                skuOptions
                    .map((sku) => sku.size)
                    .filter(
                        (size) => typeof size === "string" && size.trim() !== ""
                    )
            ),
        ].sort((a, b) => {
            const getNumericValue = (size) => {
                const match = size.match(/(\d+)/);
                return match ? Number.parseInt(match[1]) : 0;
            };
            return getNumericValue(a) - getNumericValue(b);
        });

        return [
            { value: "", label: "All Sizes" },
            ...sizes.map((size) => ({ value: size, label: size })),
        ];
    }, []);

    // Filter SKUs based on selected filters
    const filteredSKUs = useMemo(() => {
        return skuOptions
            .filter((sku) => {
                const matchesModel = !modelFilter || sku.model === modelFilter;
                const matchesColor = !colorFilter || sku.color === colorFilter;
                const matchesSize = !sizeFilter || sku.size === sizeFilter;
                return matchesModel && matchesColor && matchesSize;
            })
            .map((sku) => ({
                value: sku.value,
                label: sku.label,
            }));
    }, [modelFilter, colorFilter, sizeFilter]);

    const clearAllFilters = () => {
        setModelFilter("");
        setColorFilter("");
        setSizeFilter("");
    };

    const handleFileUpload = (e) => {
        const file = e.target.files?.[0];
        if (file) {
            setUploadedFile(file);
        }
        setData("approved_contract", file);
    };

    const handleSkuSelect = async (selectedOption) => {
        if (!selectedOption) return;

        try {
            // 1. Check inventory from server
            const response = await axios.post(
                `/item_inventories/check-inventory/${selectedOption.value}`
            );
            const { available_qty } = response.data;
            console.log(available_qty);

            if (available_qty <= 0) {
                Swal.fire(
                    "Out of Stock",
                    "The selected item has no available quantity.",
                    "warning"
                );
                return;
            }

            // 2. Add or update selected product if in stock
            setSelectedProducts((prevProducts) => {
                const existingProductIndex = prevProducts.findIndex(
                    (p) => p.value === selectedOption.value
                );

                if (existingProductIndex > -1) {
                    const updatedProducts = [...prevProducts];
                    const currentQty =
                        updatedProducts[existingProductIndex].quantity;

                    if (currentQty + 1 > available_qty) {
                        Swal.fire(
                            "Stock Limit Reached",
                            `Only ${available_qty} units are available.`,
                            "warning"
                        );
                        return prevProducts; // No update
                    }

                    updatedProducts[existingProductIndex].quantity += 1;
                    return updatedProducts;
                } else {
                    return [
                        ...prevProducts,
                        { ...selectedOption, quantity: 1 },
                    ];
                }
            });

            // 3. Clear the select input
            setSelectedItemForSelect(null);
        } catch (error) {
            console.error("Inventory check failed", error);
            Swal.fire("Error", "Failed to check inventory.", "error");
        }
    };

    const handleQuantityChange = async (skuValue, delta) => {
        try {
            // 1. Get the current stock
            const response = await axios.post(
                `/item_inventories/check-inventory/${skuValue}`
            );
            const { available_qty } = response.data;

            setSelectedProducts((prevProducts) => {
                return prevProducts
                    .map((p) => {
                        if (p.value === skuValue) {
                            const newQty = p.quantity + delta;

                            if (newQty > available_qty) {
                                Swal.fire(
                                    "Stock Limit Reached",
                                    `Only ${available_qty} units are available.`,
                                    "warning"
                                );
                                return p; // No change
                            }

                            if (newQty <= 0) {
                                return null; // Mark for removal
                            }

                            return { ...p, quantity: newQty };
                        }
                        return p;
                    })
                    .filter(Boolean); // Remove nulls
            });
        } catch (error) {
            console.error("Inventory check failed", error);
            Swal.fire("Error", "Failed to check inventory.", "error");
        }
    };

    const handleRemoveProduct = (skuValue) => {
        setSelectedProducts((prevProducts) =>
            prevProducts.filter((p) => p.value !== skuValue)
        );
    };

    useEffect(() => {
        const formattedItems = selectedProducts.map((p) => ({
            digits_code: p.value,
            quantity: p.quantity,
        }));

        setData("items", formattedItems);
    }, [selectedProducts]);


    const handleChange = (e) => {
        const name = e.name ? e.name : e.target.name;
        const value = e.value ? e.value : e.target.value;
        setData(name, value);
    };

    const handleMobileChange = (e) => {
        let value = e.target.value;

        // Ensure input starts with +639
        if (!value.startsWith("+639")) {
            value = "+639";
        }

        // Allow only digits after +639
        const digits = value.slice(4).replace(/\D/g, ""); // Remove non-digits
        value = "+639" + digits.slice(0, 9); // Limit to 9 digits after +639

        handleChange({
            target: {
                name: "contact_details",
                value: value,
            },
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        Swal.fire({
            title: `<p class="font-poppins text-3xl" >Do you want to submit this Order?</p>`,
            showCancelButton: true,
            confirmButtonText: `Submit`,
            confirmButtonColor: buttonSwalColor,
            icon: "question",
            iconColor: buttonSwalColor,
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                post("/orders/store", {
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
            <ContentPanel>
                <div className="flex justify-center items-center">
                    <div className="bg-white md:border border-gray-400 rounded-lg shadow-sm md:w-2/3">
                        <div className="p-6 border-b border-gray-400">
                            <h2 className="text-2xl font-semibold text-gray-900">
                                Customer Information Form
                            </h2>
                            <p className="text-sm text-gray-600 mt-1">
                                Please fill out all required information for
                                processing your order.
                            </p>
                        </div>
                        <div className="p-6">
                            <form onSubmit={handleSubmit} className="space-y-3">
                                {/* Customer Name */}
                                <div className="md:grid md:grid-cols-2 md:gap-1 space-y-2 md:space-y-0 w-full">
                                    <InputComponent
                                        placeholder="Enter Customer First Name"
                                        name="first_name"
                                        displayName="Customer First Name"
                                        onChange={handleChange}
                                        onError={errors.first_name}
                                    />
                                    <InputComponent
                                        placeholder="Enter Customer Last Name"
                                        name="last_name"
                                        displayName="Customer Last Name"
                                        onChange={handleChange}
                                        onError={errors.last_name}
                                    />
                                </div>

                                {/* Delivery Address */}
                                <TextArea
                                    name="delivery_address"
                                    onChange={handleChange}
                                    placeholder="Enter Complete Delivery Address"
                                    rows={3}
                                    onError={errors.delivery_address}
                                />

                                {/* Email Address */}
                                <InputComponent
                                    placeholder="Enter Email Address"
                                    name="email_address"
                                    onChange={handleChange}
                                    onError={errors.email_address}
                                />
                          
                                {/* Contact Details */}
                                <InputComponent
                                    placeholder="+639XXXXXXXXX"
                                    name="contact_details"
                                    type="tel"
                                    value={data.contact_details}
                                    onChange={handleMobileChange}
                                    onError={errors.contact_details}
                                />

                                {/* Downpayment - Yes/No */}
                                <div className='mt-2'>
                                    <label className={`block text-xs font-bold ${theme === 'bg-skin-black' ? ' text-gray-400' : 'text-gray-700'}  font-poppins`}>Downpayment</label>
                                    <div className="relative rounded-lg mt-1 flex space-x-1 overflow-hidden border-2 bg-gray-300">
                                        <div
                                            className={`absolute ${theme} rounded-md h-full w-1/2 transition-all duration-300 ${
                                            data.has_downpayment === "yes" ? "left-0" : "left-1/2"}`}
                                        >
                                        </div>
                                        <button
                                            type="button"
                                            className={` flex-1 py-1 z-10 outline-none text-sm font-medium
                                            ${data.has_downpayment === "yes" ? "text-white" : "text-black/50"}`}
                                            onClick={() => setData("has_downpayment", "yes")}
                                        >
                                            Yes
                                        </button>
                                        <button
                                            type="button"
                                            className={`flex-1 py-1.5 z-10 outline-none text-sm font-medium
                                            ${data.has_downpayment == "no" ? "text-white" : "text-black/50"}`}
                                            onClick={() => setData("has_downpayment", "no")}
                                        >
                                            No
                                        </button>
                                    </div>
                                </div>

                                {/* Downpayment Value (conditional) */}
                                {data.has_downpayment === "yes" && (
                                    <InputComponent
                                        placeholder="Enter Downpayment Amount"
                                        name="downpayment_value"
                                        onChange={handleChange}
                                        onError={errors.downpayment_value}
                                    />
                                )}

                                {/* Financed Amount */}
                                <InputComponent
                                    placeholder="Enter Financed Amount"
                                    name="financed_amount"
                                    onChange={handleChange}
                                    onError={errors.financed_amount}
                                />

                                {/* SKU Details with Filters */}
                                <div className="space-y-3">
                                    <label className="block text-xs font-bold text-gray-700">
                                        SKU Filter
                                    </label>

                                    {/* Filter Controls */}
                                    <div className="bg-gray-50 p-4 rounded-lg border">
                                        <div className="flex items-center justify-between mb-3">
                                            <h4 className="text-sm font-medium text-gray-700">
                                                Filter Products
                                            </h4>
                                            <button
                                                type="button"
                                                onClick={clearAllFilters}
                                                className="text-xs text-blue-600 hover:text-blue-800 underline"
                                            >
                                                Clear All Filters
                                            </button>
                                        </div>

                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            {/* Model Filter */}
                                            <CustomSelect
                                                displayName="Model"
                                                value={uniqueModels.find((option) =>option.value === modelFilter)}
                                                onChange={(option) =>setModelFilter(option?.value || "")}
                                                options={uniqueModels}
                                                placeholder="Select model..."
                                                maxMenuHeight="300px"
                                            />

                                            {/* Color Filter */}
                                            <CustomSelect
                                                displayName="Color"
                                                value={uniqueColors.find((option) =>option.value === colorFilter)}
                                                onChange={(option) => setColorFilter(option?.value || "")}
                                                options={uniqueColors}
                                                placeholder="Select color..."
                                                maxMenuHeight="300px"
                                            />

                                            {/* Size Filter */}
                                            <CustomSelect
                                                displayName="Storage/Size"
                                                value={uniqueSizes.find((option) => option.value === sizeFilter)}
                                                onChange={(option) => setSizeFilter(option?.value || "")}
                                                options={uniqueSizes}
                                                placeholder="Select size..."
                                                maxMenuHeight="300px"
                                            />
                                        </div>

                                        <div className="mt-2 text-xs text-gray-500">
                                            Showing {filteredSKUs.length} of{" "}
                                            {skuOptions.length} products
                                        </div>
                                    </div>

                                    <CustomSelect
                                        name="digits_code"
                                        placeholder="Search and select a product SKU..."
                                        displayName="Item Details"
                                        value={selectedItemForSelect}
                                        onChange={(option) =>
                                            handleSkuSelect(option)
                                        }
                                        options={filteredSKUs}
                                        maxMenuHeight="300px"
                                    />
                                    {/* Selected Products Table */}
                                    {selectedProducts.length > 0 && (
                                        <div className="mt-6 border border-gray-200 rounded-lg overflow-hidden">
                                            <table className="min-w-full divide-y divide-gray-200">
                                                <thead className="bg-gray-50">
                                                    <tr>
                                                        <th
                                                            scope="col"
                                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                        >
                                                            Item Description
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                        >
                                                            Qty
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                                        >
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody className="bg-white divide-y divide-gray-200">
                                                    {selectedProducts.map(
                                                        (product) => (
                                                            <tr
                                                                key={
                                                                    product.value
                                                                }
                                                            >
                                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                    {
                                                                        product.label
                                                                    }
                                                                </td>
                                                                <td className="px-6 py-4 whitespace-nowrap text-center">
                                                                    <div className="flex items-center justify-center space-x-2">
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                handleQuantityChange(
                                                                                    product.value,
                                                                                    -1
                                                                                )
                                                                            }
                                                                            className="p-1 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                            aria-label={`Decrease quantity of ${product.label}`}
                                                                        >
                                                                            <svg
                                                                                className="h-4 w-4"
                                                                                fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                            >
                                                                                <path
                                                                                    strokeLinecap="round"
                                                                                    strokeLinejoin="round"
                                                                                    strokeWidth={
                                                                                        2
                                                                                    }
                                                                                    d="M20 12H4"
                                                                                />
                                                                            </svg>
                                                                        </button>
                                                                        <span className="text-sm font-medium text-gray-900 w-8 text-center">
                                                                            {
                                                                                product.quantity
                                                                            }
                                                                        </span>
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                handleQuantityChange(
                                                                                    product.value,
                                                                                    1
                                                                                )
                                                                            }
                                                                            className="p-1 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                                            aria-label={`Increase quantity of ${product.label}`}
                                                                        >
                                                                            <svg
                                                                                className="h-4 w-4"
                                                                                fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24"
                                                                                xmlns="http://www.w3.org/2000/svg"
                                                                            >
                                                                                <path
                                                                                    strokeLinecap="round"
                                                                                    strokeLinejoin="round"
                                                                                    strokeWidth={
                                                                                        2
                                                                                    }
                                                                                    d="M12 4v16m8-8H4"
                                                                                />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                                    <button
                                                                        type="button"
                                                                        onClick={() =>
                                                                            handleRemoveProduct(
                                                                                product.value
                                                                            )
                                                                        }
                                                                        className="text-red-600 hover:text-red-900 text-xs font-medium"
                                                                    >
                                                                        Remove
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        )
                                                    )}
                                                </tbody>
                                            </table>
                                        </div>
                                    )}

                                    {/* Upload of Approved Contract */}
                                    <div className="space-y-2">
                                        <label
                                            htmlFor="approved_contract"
                                            className="block text-xs font-bold text-gray-700"
                                        >
                                            Upload of Approved Contract
                                        </label>
                                        <div className={`relative border-2 ${errors.approved_contract ? 'border-red-500' : 'border-dashed border-gray-400 hover:border-gray-400'}  rounded-lg p-6 text-center  transition-colors cursor-pointer`}>
                                            <input
                                                id="approved_contract"
                                                name="approved_contract"
                                                type="file"
                                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                onChange={handleFileUpload}
                                                className="hidden"
                                            />
                                            <label
                                                htmlFor="approved_contract"
                                                className="cursor-pointer flex flex-col items-center gap-2"
                                            >
                                                {uploadedFile ? (
                                                    <>
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
                                                            Click to upload
                                                            contract
                                                        </span>
                                                        <span className="text-xs text-gray-500">
                                                            PDF, DOC, DOCX, JPG,
                                                            PNG up to 10MB
                                                        </span>
                                                    </>
                                                )}
                                            </label>
                                             {errors.approved_contract && 
                                                <LoginInputTooltip content={errors.approved_contract}>
                                                <i
                                                    className="fa-solid fa-circle-info text-red-600 absolute cursor-pointer top-1/2 text-xs md:text-base right-1.5 md:right-3 transform -translate-y-1/2">
                                                </i>
                                                </LoginInputTooltip>
                                            }
                                        </div>
                                    </div>
                                </div>

                                {/* Submit Button */}
                                <div className="flex gap-4 pt-4">
                                    <button
                                        type="button"
                                        className="flex-1 px-4 py-2 border border-gray-400 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        className="flex-1 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-black/70 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors"
                                    >
                                        Submit Form
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </ContentPanel>
        </>
    );
};

export default CreateOrderForm;
