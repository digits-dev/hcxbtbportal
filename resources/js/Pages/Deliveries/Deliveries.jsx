import { Head, Link, router, usePage } from "@inertiajs/react";
import React, { useEffect, useState } from "react";
import ContentPanel from "../../Components/Table/ContentPanel";
import Tooltip from "../../Components/Tooltip/Tooltip";
import Button from "../../Components/Table/Buttons/Button";
import TopPanel from "../../Components/Table/TopPanel";
import Filters from "../../Components/Filters/Filters";
import Export from "../../Components/Table/Buttons/Export";
import { useTheme } from "../../Context/ThemeContext";
import useThemeStyles from "../../Hooks/useThemeStyles";
import TableSearch from "../../Components/Table/TableSearch";
import TableContainer from "../../Components/Table/TableContainer";
import Thead from "../../Components/Table/Thead";
import Row from "../../Components/Table/Row";
import TableHeader from "../../Components/Table/TableHeader";
import Tbody from "../../Components/Table/Tbody";
import RowStatus from "../../Components/Table/RowStatus";
import Pagination from "../../Components/Table/Pagination";
import RowData from "../../Components/Table/RowData";
import RowAction from "../../Components/Table/RowAction";
import CustomFilter from "../../Components/Table/Buttons/CustomFilter";
import moment from "moment/moment";

const Deliveries = ({page_title, queryParams, deliveries}) => {

    const { auth } = usePage().props;
    const { theme } = useTheme();
    const { primayActiveColor, textColorActive } = useThemeStyles(theme);
    const [pathname, setPathname] = useState(null);

    useEffect(() => {
        const segments = window.location.pathname.split("/");
        setPathname(segments.pop());
    }, []);

    const refreshTable = (e) => {
        e.preventDefault();
        router.get(pathname);
    };

    return (
        <>
            <Head title={page_title}/>
            <ContentPanel>
                <TopPanel>
                    <div className="inline-flex flex-wrap gap-1">
                        <Tooltip text="Refresh data" arrow="bottom">
                            <Button
                                extendClass={
                                    (["bg-skin-white"].includes(theme)
                                        ? primayActiveColor
                                        : theme) + " py-[5px] px-[10px]"
                                }
                                fontColor={textColorActive}
                                onClick={refreshTable}
                            >
                                <i className="fa fa-rotate-right text-base p-[1px]"></i>
                            </Button>
                        </Tooltip>
                        <Export path="/item_masters/export" page_title={page_title}/>
                    </div>
                    <div className="flex">
                        <CustomFilter>
                            <Filters filter_inputs={[
                                {name: 'Reference Number', column: 'reference_number'},
                                {name: 'Received Date', column: 'received_at'},
                                {name: 'Created Date', column: 'created_at'},
                            ]}/>
                        </CustomFilter>
                        <TableSearch queryParams={queryParams} />
                    </div>
                </TopPanel>
                <TableContainer data={deliveries?.data}>
                    <Thead>
                        <Row>
                            <TableHeader
                                sortable={false}
                                width="md"
                                justify="center"
                            >
                                Action
                            </TableHeader>
                            <TableHeader
                                name="digits_code"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Reference Number
                            </TableHeader>
                            <TableHeader
                                name="received_at"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Received Date
                            </TableHeader>
                            <TableHeader
                                name="updated_at"
                                queryParams={queryParams}
                                width="lg"
                            >
                                Created Date
                            </TableHeader>
                        </Row>
                    </Thead>
                    <Tbody data={deliveries?.data}>
                        {deliveries &&
                            deliveries?.data.map((item, index) => (
                                <Row key={item.id}>
                                    <RowData center>
                                        <RowAction
                                            type="link"
                                            action="view"
                                            href={`deliveries/delivery_details/${item.id}`}
                                        />
                                    </RowData>
                                    <RowData>
                                        {item.reference_number ?? '-'}
                                    </RowData>
                                    <RowData>
                                        {item.received_at ? (moment(item.received_at).format("YYYY-MM-DD HH:mm:ss")) : '-'}
                                    </RowData>
                                    <RowData>
                                        {item.created_at ? (moment(item.created_at).format("YYYY-MM-DD HH:mm:ss")) : '-'}
                                    </RowData>
                                </Row>
                            ))}
                    </Tbody>
                </TableContainer>
                <Pagination extendClass={theme} paginate={deliveries} />
            </ContentPanel>
        </>
    );
};

export default Deliveries;
