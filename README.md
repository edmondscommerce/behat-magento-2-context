#Magento Two Context
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

Behat contexts to aid testing of Magento 2.x sites on both the frontend and admin via blackbox methods only

### Installation

Install via composer

composer require edmondscommerce/behat-magento-2-context


### Include Contexts in Behat Configuration

```
default:
    # ...
    suites:
        default:
        # ...
            contexts:
                - # ...
                - EdmondsCommerce\BehatMagentoTwoContext\ProductContext
                - EdmondsCommerce\BehatMagentoTwoContext\CheckoutContext
                - EdmondsCommerce\BehatMagentoTwoContext\AdminContext
            parameters:
                magentoSettings:
                    adminUri: admin/
                    userName: admin
                    password: password
                    simpleUri: fusion-backpack.html
                    bundleUri: pillow-and-throw-set.html
                    configurableUri: lafayette-convertible-dress.html
                    groupedUri: vase-set.html
                    successPageTitle: Success Page

```

The context assumes that for Admin panel is using the default theme and the front end theme is largely based on the luma theme.