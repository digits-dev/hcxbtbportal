import { Head, useForm } from '@inertiajs/react'
import ContentPanel from "../../Components/Table/ContentPanel"
import { useTheme } from "../../Context/ThemeContext";
import { useState, useMemo } from "react"
import { useToast } from "../../Context/ToastContext";
import Select from 'react-select'
import CustomSelect from '../../Components/Dropdown/CustomSelect';


const CreateOrderForm = ({page_title, sku}) => {
    const { handleToast } = useToast();
    const [uploadedFile, setUploadedFile] = useState(null);
    const [modelFilter, setModelFilter] = useState("")
    const [colorFilter, setColorFilter] = useState("")
    const [sizeFilter, setSizeFilter] = useState("")

    const skuOptions = sku.map(item => ({
        value: item.id,
        label: item.item_description,
        model: item.model,
        color: item.actual_color,
        size: item.size,
   }));

   // Get unique values for filter dropdowns
  const uniqueModels = useMemo(() => {
    const models = [...new Set(skuOptions.map((sku) => sku.model))].sort()
    return [{ value: "", label: "All Models" }, ...models.map((model) => ({ value: model, label: model }))]
  }, [])

  const uniqueColors = useMemo(() => {
    const colors = [...new Set(skuOptions.map((sku) => sku.color))].sort()
    return [{ value: "", label: "All Colors" }, ...colors.map((color) => ({ value: color, label: color }))]
  }, [])

 const uniqueSizes = useMemo(() => {
  const sizes = [
    ...new Set(
      skuOptions
        .map((sku) => sku.size)
        .filter((size) => typeof size === "string" && size.trim() !== "")
    )
  ].sort((a, b) => {
    const getNumericValue = (size) => {
      const match = size.match(/(\d+)/)
      return match ? Number.parseInt(match[1]) : 0
    }
    return getNumericValue(a) - getNumericValue(b)
  })

  return [
    { value: "", label: "All Sizes" },
    ...sizes.map((size) => ({ value: size, label: size }))
  ]
}, [])

   // Filter SKUs based on selected filters
  const filteredSKUs = useMemo(() => {
    return skuOptions
      .filter((sku) => {
        const matchesModel = !modelFilter || sku.model === modelFilter
        const matchesColor = !colorFilter || sku.color === colorFilter
        const matchesSize = !sizeFilter || sku.size === sizeFilter
        return matchesModel && matchesColor && matchesSize
      })
      .map((sku) => ({
        value: sku.value,
        label: sku.label,
      }))
  }, [modelFilter, colorFilter, sizeFilter])


  const handleFileUpload = (e) => {
  console.log(data);
    const file = e.target.files?.[0]
    if (file) {
      setUploadedFile(file)
    }
  setData("approved_contract", file)
  }

    const { data, setData, post, processing, errors, reset } = useForm({
        customer_name: "",
        delivery_address: "",
        email_address: "",
        contact_details: "",
        financed_amount: "",
        has_downpayment: "no", 
        downpayment_value: "",
        approved_contract: "",
        item_id: "",
    });

  const handleChange = (e) => {
    const name = e.name ? e.name : e.target.name;
    const value = e.value ? e.value : e.target.value;
    setData(name, value);

  }

  const handleSubmit = (e) => {
    e.preventDefault()
      post("/orders/store", {
      onSuccess: (response) => {
          console.log(response);
          handleToast("Order added successfully", "success");
        },
      });
  }

    const clearAllFilters = () => {
    setModelFilter("")
    setColorFilter("")
    setSizeFilter("")
  }
 

    return <>
      <Head title={page_title}/>
    <ContentPanel>
    <div className="flex justify-center items-center">
      <div className="bg-white md:border border-gray-400 rounded-lg shadow-sm md:w-2/3">
        <div className="p-6 border-b border-gray-400">
          <h2 className="text-2xl font-semibold text-gray-900">Customer Information Form</h2>
          <p className="text-sm text-gray-600 mt-1">
            Please fill out all required information for processing your order.
          </p>
        </div>
        <div className="p-6">
          <form onSubmit={handleSubmit} className="space-y-6">
            {/* Customer Name */}
            <div className="space-y-2">
              <label htmlFor="customer_name" className="block text-sm font-medium text-gray-700">
                Customer Name
              </label>
              <input
                id="customer_name"
                name="customer_name"
                type="text"
                placeholder="Enter full name"
                onChange={handleChange}
                required
                className="w-full px-3 py-2 border border-gray-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            {/* Delivery Address */}
            <div className="space-y-2">
              <label htmlFor="delivery_address" className="block text-sm font-medium text-gray-700">
                Delivery Address
              </label>
              <textarea
                id="delivery_address"
                name="delivery_address"
                placeholder="Enter complete delivery address"
                onChange={handleChange}
                rows={3}
                required
                className="w-full px-3 py-2 border border-gray-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-vertical"
              />
            </div>

            {/* Email Address */}
            <div className="space-y-2">
              <label htmlFor="email_address" className="block text-sm font-medium text-gray-700">
                Email Address
              </label>
              <input
                id="email_address"
                name="email_address"
                type="email"
                placeholder="Enter email address"
                onChange={handleChange}
                required
                className="w-full px-3 py-2 border border-gray-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            {/* Contact Details */}
            <div className="space-y-2">
              <label htmlFor="contact_details" className="block text-sm font-medium text-gray-700">
                Contact Details
              </label>
              <input
                id="contact_details"
                name="contact_details"
                type="text"
                onChange={handleChange}
                placeholder="Enter phone number or other contact information"
                required
                className="w-full px-3 py-2 border border-gray-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

            {/* Downpayment - Yes/No */}
            <div className="space-y-3">
              <label className="block text-sm font-medium text-gray-700">Downpayment</label>
              <div className="flex gap-6">
                <div className="flex items-center space-x-2">
                  <input
                    type="radio"
                    id="downpayment-yes"
                    name="has_downpayment"
                    value="yes"
                    checked={data.has_downpayment === "yes"}
                    onChange={(e) => setData("has_downpayment", e.target.value)}
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-400"
                  />
                  <label htmlFor="downpayment-yes" className="text-sm text-gray-700">
                    Yes
                  </label>
                </div>
                <div className="flex items-center space-x-2">
                  <input
                    type="radio"
                    id="downpayment-no"
                    name="has_downpayment"
                    value="no"
                    checked={data.has_downpayment === "no"}
                    onChange={(e) => setData("has_downpayment", e.target.value)}
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-400"
                  />
                  <label htmlFor="downpayment-no" className="text-sm text-gray-700">
                    No
                  </label>
                </div>
              </div>
            </div>

            {/* Downpayment Value (conditional) */}
            {data.has_downpayment === "yes" && (
              <div className="space-y-2">
                <label htmlFor="downpayment_value" className="block text-sm font-medium text-gray-700">
                  Downpayment Value (if with DP)
                </label>
                <input
                  id="downpayment_value"
                  name="downpayment_value"
                  type="number"
                  placeholder="Enter downpayment amount"
                  onChange={handleChange}
                  required
                  className="w-full px-3 py-2 border border-gray-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            )}

            {/* Financed Amount */}
            <div className="space-y-2">
              <label htmlFor="financed_amount" className="block text-sm font-medium text-gray-700">
                Financed Amount
              </label>
              <input
                id="financed_amount"
                name="financed_amount"
                type="number"
                placeholder="Enter financed amount"
                onChange={handleChange}
                required
                className="w-full px-3 py-2 border border-gray-400 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>

               {/* SKU Details with Filters */}
            <div className="space-y-4">
              <label className="block text-sm font-medium text-gray-700">SKU Filter</label>

              {/* Filter Controls */}
              <div className="bg-gray-50 p-4 rounded-lg border">
                <div className="flex items-center justify-between mb-3">
                  <h4 className="text-sm font-medium text-gray-700">Filter Products</h4>
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
                  <div>
                    <label className="block text-xs font-medium text-gray-600 mb-1">Model</label>
                      <CustomSelect 
                        value={uniqueModels.find((option) => option.value === modelFilter)}
                        onChange={(option) => setModelFilter(option?.value || "")}
                        options={uniqueModels}
                        placeholder="Select model..."
                        maxMenuHeight='300px'
                      />
                  </div>

                  {/* Color Filter */}
                  <div>
                    <label className="block text-xs font-medium text-gray-600 mb-1">Color</label>
                    <CustomSelect 
                            value={uniqueColors.find((option) => option.value === colorFilter)}
                            onChange={(option) => setColorFilter(option?.value || "")}
                            options={uniqueColors}
                            placeholder="Select color..."
                            maxMenuHeight='300px'
                      />
                  </div>

                  {/* Size Filter */}
                  <div>
                    <label className="block text-xs font-medium text-gray-600 mb-1">Storage/Size</label>
                      <CustomSelect 
                        value={uniqueSizes.find((option) => option.value === sizeFilter)}
                        onChange={(option) => setSizeFilter(option?.value || "")}
                        options={uniqueSizes}
                        placeholder="Select size..."
                        maxMenuHeight='300px'
                      />
                  </div>
                </div>

                <div className="mt-2 text-xs text-gray-500">
                  Showing {filteredSKUs.length} of {skuOptions.length} products
                </div>
              </div>

            <CustomSelect 
            name="item_id"
            placeholder="Search and select a product SKU..."
            displayName='Item Details'
            options={filteredSKUs}
            onChange={handleChange}
            maxMenuHeight='300px'
            />
              
            {/* Upload of Approved Contract */}
            <div className="space-y-2">
              <label htmlFor="approved_contract" className="block text-sm font-medium text-gray-700">
                Upload of Approved Contract
              </label>
              <div className="border-2 border-dashed border-gray-400 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer">
                <input
                  id="approved_contract"
                  name="approved_contract"
                  type="file"
                  accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                  onChange={handleFileUpload}
                  className="hidden"
                />
                <label htmlFor="approved_contract" className="cursor-pointer flex flex-col items-center gap-2">
                  {uploadedFile ? (
                    <>
                      <svg className="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth={2}
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                      </svg>
                      <span className="text-sm font-medium text-green-600">{uploadedFile.name}</span>
                      <span className="text-xs text-gray-500">Click to change file</span>
                    </>
                  ) : (
                    <>
                      <svg className="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                          strokeLinecap="round"
                          strokeLinejoin="round"
                          strokeWidth={2}
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                        />
                      </svg>
                      <span className="text-sm font-medium text-gray-700">Click to upload contract</span>
                      <span className="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG up to 10MB</span>
                    </> 
                  )}
                </label>
              </div>
            </div>

          
            </div>


            {/* Submit Button */}
            <div className="flex gap-4 pt-4">
              <button
                type="button"
                className="flex-1 px-4 py-2 border border-gray-400 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                className="flex-1 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
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
}

export default CreateOrderForm